import React, { useState } from 'react';
import moment from 'moment';
import Translator from "@hubleto/react-ui/core/Translator";
import request from '@hubleto/react-ui/core/Request';
import Form, { type FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import { useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';
import { type FormMeta, type FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import InputTags from '@hubleto/react-ui/components/fc/Inputs/Tags';
import Modal from '@hubleto/react-ui/components/fc/Modal';
import CalendarTab from '@hubleto/apps/Calendar/Components/FC/CalendarTab';
import MessageCalendarActivityForm from './MessageCalendarActivityForm';
import TableReplies from './TableReplies';

export interface FormMessageProps extends FormProps {}

const componentName = 'FormMessage'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Custom/LinkedinMessages';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** Title */
const Title = () => {
  const subject: string = useRecordField('subject', '');
  const profile: any = useRecordField('PROFILE', null);
  const direction: number = useRecordField('direction', 1);
  const who = profile ? [profile.first_name, profile.last_name].filter((x) => x).join(' ') : '';

  return <div>
    <h2>{subject || who || T.translate('Message')}</h2>
    <div className='badge'>
      {direction == 2 ? T.translate('Outgoing') : T.translate('Incoming')}
      {who && subject ? ' · ' + who : ''}
    </div>
  </div>;
}

/** TabDefault */
const TabDefault = (props: FormMessageProps) => {
  const form: FormMeta = React.useContext(FormMetaContext);
  const loadedId: number = useRecordField('id', 0);
  const ACTIVITIES: any = useRecordField('ACTIVITIES', {});
  const TAGS: Array<any> = useRecordField('TAGS', []);
  const isRead: boolean = useRecordField('is_read', false);
  const direction: number = useRecordField('direction', 1);
  const profile: any = useRecordField('PROFILE', null);

  let nextActivity: any = null;
  let nextActivityDate: any = null;

  if (ACTIVITIES) {
    Object.keys(ACTIVITIES).map((key) => {
      if (nextActivityDate !== null) return;
      const activity = ACTIVITIES[key];
      const dateStart = moment(activity.date_start);
      if (!activity.completed && dateStart.isAfter()) {
        nextActivity = activity;
        nextActivityDate = dateStart;
      }
    });
  }

  // opening an unread incoming message marks it as read
  React.useEffect(() => {
    if (loadedId > 0 && !isRead && direction == 1) {
      request.get('linkedin-messages/api/set-read', { idMessage: loadedId, read: 1 });
    }
  }, [loadedId]);

  const toggleRead = () => {
    request.get('linkedin-messages/api/set-read', { idMessage: form.id, read: isRead ? 0 : 1 }, () => form.reload());
  }

  return <div className='flex flex-col gap-2 md:flex-row'>
    <div className='flex-1'>
      {profile ? <div className='mb-2'>
        <b>{[profile.first_name, profile.last_name].filter((x) => x).join(' ')}</b>
        {profile.headline ? <div className='text-xs text-gray-500'>{profile.headline}</div> : null}
        {profile.profile_url ? <a className='text-xs' href={profile.profile_url} target='_blank'>
          <i className='fab fa-linkedin mr-1'></i>{T.translate('Open on LinkedIn')}
        </a> : null}
      </div> : null}
      <Input field='id_profile' />
      <Input field='subject' />
      <Input field='body' customInputProps={{ cssClass: 'bg-gray-50 dark:bg-slate-600' }} />
      <Input title={T.translate('Tags')}>
        <InputTags
          field='TAGS'
          value={TAGS}
          model='Hubleto/App/Custom/LinkedinMessages/Models/Tag'
          targetColumn='id_message'
          sourceColumn='id_tag'
          colorColumn='_LOOKUP_COLOR'
          showSelect={false}
          showTagButtons={true}
          onChange={(input: any, value: any) => {
            form.changeField(input, value);
          }}
          onNewTag={(title: string) => {
            return { id: -1, name: title, color: '#' + Math.floor(Math.random()*16777215).toString(16).padStart(6, '0') }
          }}
        ></InputTags>
      </Input>
      <Input field='note' customInputProps={{ cssClass: 'bg-yellow-50 dark:bg-slate-600' }} />
    </div>
    <div className='flex-1'>
      {form.id > 0 ? <>
        {nextActivityDate ?
          <div className='block alert alert-success'>
            <i className='fas fa-calendar mr-2'></i>
            {T.translate('Next follow-up is planned for')} <b>{nextActivityDate.format('YYYY-MM-DD')}</b>.<br/>
            <br/>
            <i>{nextActivity.subject}</i>
          </div>
        : <div className='block alert alert-danger'>
            <i className='fas fa-calendar mr-2'></i>
            {T.translate('No follow-up in your calendar.')}
          </div>
        }
        <button className='btn btn-transparent mb-2' onClick={toggleRead}>
          <span className='icon'><i className={'fas ' + (isRead ? 'fa-envelope' : 'fa-envelope-open')}></i></span>
          <span className='text'>{isRead ? T.translate('Mark as unread') : T.translate('Mark as read')}</span>
        </button>
      </> : null}
      <Input field='id_campaign' />
      <Input field='direction' />
      <Input field='sent_at' />
      <Input field='is_read' />
      <Input field='id_account' />
      <Input field='id_owner' />
    </div>
  </div>;
}

/** TabReply: compose and send a reply, then plan a follow-up */
const TabReply = (props: FormMessageProps) => {
  const form: FormMeta = React.useContext(FormMetaContext);
  const profile: any = useRecordField('PROFILE', null);

  const [text, setText] = useState('');
  const [lastText, setLastText] = useState('');
  const [sending, setSending] = useState(false);
  const [result, setResult] = useState<any>(null);
  const [showFollowup, setShowFollowup] = useState(false);
  const [repliesKey, setRepliesKey] = useState(0);

  if (form.id <= 0) return <div className='alert alert-info'>{T.translate('Save the message first')}</div>;

  const who = profile ? [profile.first_name, profile.last_name].filter((x) => x).join(' ') : '';

  const send = () => {
    if (text.trim() == '') return;
    setLastText(text);
    setSending(true);
    request.post(
      'linkedin-messages/api/send-reply',
      { idMessage: form.id, body: text },
      {},
      (data: any) => {
        setSending(false);
        if (data.status != 'success') {
          setResult({ result: 'error', message: data.message });
          return;
        }
        setResult(data);
        setText('');
        setRepliesKey(repliesKey + 1);
        // after a reply was delivered there is nothing else to do, so ask for the follow-up right away
        if (data.result == 'sent' && !data.hasFutureFollowup) setShowFollowup(true);
      },
      () => setSending(false)
    );
  }

  const markSent = () => {
    request.get('linkedin-messages/api/mark-reply-sent', { idReply: result.idReply }, () => {
      setResult({ ...result, result: 'sent', message: T.translate('Reply was marked as sent.') });
      setRepliesKey(repliesKey + 1);
    });
  }

  const copyReply = () => {
    if (lastText && navigator.clipboard) navigator.clipboard.writeText(lastText);
  }

  const alertClass = result?.result == 'sent' ? 'alert-success' : (result?.result == 'error' || result?.result == 'failed') ? 'alert-danger' : 'alert-warning';

  return <div className='flex flex-col gap-2'>
    <div className='card'>
      <div className='card-header'>{T.translate('Reply to')} {who}</div>
      <div className='card-body'>
        <textarea
          className='w-full p-2 border rounded'
          rows={6}
          placeholder={T.translate('Write your reply (plain text)')}
          value={text}
          onChange={(e) => setText(e.target.value)}
        ></textarea>
        <button className='btn btn-add mt-2' disabled={sending || text.trim() == ''} onClick={send}>
          <span className='icon'><i className='fas fa-paper-plane'></i></span>
          <span className='text'>{sending ? T.translate('Sending...') : T.translate('Send reply')}</span>
        </button>
      </div>
    </div>

    {result ? <div className={'alert ' + alertClass}>
      <div><b>{result.message}</b></div>
      {result.details ? <div className='text-xs mt-1'>{result.details}</div> : null}

      {(result.result == 'manual' || result.result == 'failed') ? <div className='flex flex-row gap-2 mt-2'>
        <button className='btn btn-transparent' onClick={copyReply}>
          <span className='icon'><i className='fas fa-copy'></i></span>
          <span className='text'>{T.translate('Copy reply')}</span>
        </button>
        <a className='btn btn-transparent' href={result.profileUrl || result.conversationUrl} target='_blank'>
          <span className='icon'><i className='fab fa-linkedin'></i></span>
          <span className='text'>{T.translate('Open LinkedIn')}</span>
        </a>
        <button className='btn btn-transparent' onClick={markSent}>
          <span className='icon'><i className='fas fa-check'></i></span>
          <span className='text'>{T.translate('I sent it')}</span>
        </button>
      </div> : null}

      {result.promptFollowup ? <div className='mt-3 p-2 bg-white dark:bg-slate-700 rounded'>
        <i className='fas fa-calendar mr-2'></i>
        {result.hasFutureFollowup
          ? T.translate('A follow-up is already planned. Do you want to plan another one?')
          : T.translate('No follow-up is planned yet. Plan one now so that this conversation does not get lost.')}
        <div className='mt-2'>
          <button className='btn btn-add' onClick={() => setShowFollowup(true)}>
            <span className='icon'><i className='fas fa-calendar-plus'></i></span>
            <span className='text'>{T.translate('Plan follow-up')}</span>
          </button>
        </div>
      </div> : null}
    </div> : null}

    <div className='card'>
      <div className='card-header'>{T.translate('Replies')}</div>
      <div className='card-body'>
        <div key={repliesKey}>
          <TableReplies
            tag={"table_message_replies"}
            parentForm={form}
            uid={form.uid + "_table_message_replies_" + repliesKey}
            idMessage={form.id}
          />
        </div>
      </div>
    </div>

    {showFollowup ? <Modal
      uid='linkedin_followup_prompt'
      isOpen={true}
      type='right'
      onClose={() => setShowFollowup(false)}
    >
      <MessageCalendarActivityForm
        id={-1}
        idMessage={form.id}
        defaultValues={{
          subject: T.translate('Follow up on LinkedIn conversation') + (who ? ': ' + who : ''),
          date_start: moment().add(1, 'week').format('YYYY-MM-DD'),
          date_end: moment().add(1, 'week').format('YYYY-MM-DD'),
          time_start: '10:00:00',
          time_end: '10:15:00',
          all_day: false,
        }}
        onClose={() => setShowFollowup(false)}
        onAfterSaveRecord={() => { setShowFollowup(false); form.reload(); }}
      ></MessageCalendarActivityForm>
    </Modal> : null}
  </div>;
}

/** TabCalendar */
const TabCalendar = (props: FormMessageProps) => {
  const form: FormMeta = React.useContext(FormMetaContext);
  if (form.id <= 0) return <div className='alert alert-info'>{T.translate('Save the message first')}</div>;
  return <CalendarTab
    loadEventsEndpoint={'calendar/api/get-calendar-events?calendar=linkedin-messages&idMessage=' + form.id}
    logActivityEndpoint={'linkedin-messages/api/log-activity?idMessage=' + form.id}
    renderActivityForm={(calendarTab: any) => {
      return <MessageCalendarActivityForm idMessage={form.id} calendarTab={calendarTab}></MessageCalendarActivityForm>;
    }}
  ></CalendarTab>;
}

/** FormMessage */
const FormMessage = (props: FormMessageProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Message'}
    urlSlug='linkedin-messages'
    endpointParams={{saveRelations: ['TAGS'] }}
    tabs={{
      default: { title: <b>{T.translate('Message')}</b>, content: () => <TabDefault {...props} /> },
      reply: { title: T.translate('Reply'), content: () => <TabReply {...props} /> },
      calendar: { title: T.translate('Follow-ups'), content: () => <TabCalendar {...props} /> },
    }}
    renderTitle={() => <Title />}
    {...props}
  ></Form>;
}

export default FormMessage;
