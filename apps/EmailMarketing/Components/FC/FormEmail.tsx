import React, { useState, useEffect } from 'react';
import TableContacts from '@hubleto/apps/Contacts/Components/FC/TableContacts';
import TableRecipients from './TableRecipients';
import TableEmailClicks from '@hubleto/apps/EmailMarketing/Components/FC/TableEmailClicks';
import request from '@hubleto/react-ui/core/Request';
import InputJsonKeyValue from "@hubleto/react-ui/components/cc/Inputs/JsonKeyValue";
import moment from "moment";
import { FormMeta, FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Translator from '@hubleto/react-ui/core/Translator';
import { useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import Spinner from '@hubleto/react-ui/components/fc/Spinner';

export interface FormEmailProps extends FormProps {}

const componentName = 'FormEmail';
const parentApp = 'Hubleto/App/Community/EmailMarketing';
const T = new Translator(parentApp + '/Loader', 'Components/FormEmail');

/** TabDefault */
const TabDefault = () => {
  return <>
    <div className='w-full flex flex-col md:flex-row gap-2'>
      <div className='flex-4 border-r border-gray-100'>
        <Input field='mail_subject' renderOnlyInputField customInputProps={{cssClass: 'text-[2em] border border-primary p-1 shadow rounded'}} />
        {/* <Input field='title' renderOnlyInputField customInputProps={{cssClass: 'text-[2em] border border-primary p-1 shadow rounded'}} /> */}
        <Input field='id_sender_account' />
        <Input field='reply_to' />
        <Input field='mail_body' />
      </div>
      <div className='flex-1'>
        <Input field='is_approved' customInputProps={{yesText: 'Approved'}} />
        <Input field='target_audience' />
        <Input field='goal' />
        <Input field='notes' />
        <Input field='utm_source' />
        <Input field='utm_campaign' />
        <Input field='utm_term' />
        <Input field='utm_content' />
        <Input field='datetime_created' />
        <Input field='uid' />
      </div>
    </div>
  </>;
}

/** TabContacts */
const TabContacts = () => {
  const form = React.useContext(FormMetaContext);
  const RECIPIENTS: Array<any> = useRecordField('RECIPIENTS');

  return <div>
    <div>
      {T.translate('Select contacts which will be added as recipients')}
    </div>
    <TableContacts
      tag={"table_email_contact"}
      parentForm={form}
      uid={form.uid + "_table_email_contact"}
      selectionMode='multiple'
      readonly={true}
      descriptionSource='both'
      //@ts-ignore
      description={{ui: {showHeader: false}}}
      idCustomer={0}
      selection={RECIPIENTS ? RECIPIENTS.map((item) => { return { id: item.id_contact } }) : null}
      onSelectionChange={(table: any) => {
        request.post(
          'email-marketing/api/save-recipients-from-contacts',
          {
            idEmail: form.id,
            contactIds: table.state.selection.map((item) => item.id)
          },
          {},
          (result: any) => {
            form.changeRecord({RECIPIENTS: result});
          }
        );
      }}
    />
  </div>;
}

/** TabRecipients */
const TabRecipients = ({ formEmail }) => {
  const form = React.useContext(FormMetaContext);
  const refTableRecipients: any = React.createRef();
  const refEmails: any = React.createRef();

  return <div className='flex gap-2'>
    <div className='flex-3'>
      <TableRecipients
        tag='table_email_recipients'
        //@ts-ignore
        ref={refTableRecipients}
        parentForm={form}
        uid={form.uid + "_table_email_recipient"}
        idEmail={form.id}
        view='briefOverview'
      />
    </div>
    <div className='flex-1 gap-2'>
      <div className='card'>
        <div className='card-header'>{T.translate('Import emails')}</div>
        <div className='card-body'>
          <textarea
            className='w-full h-80'
            placeholder={T.translate('One email per line.')}
            ref={refEmails}
          ></textarea>
          <button
            className='btn btn-add-outline mt-2 w-full'
            onClick={() => {
              request.post(
                'email-marketing/api/import-recipients',
                {
                  idEmail: form.id,
                  recipients: refEmails.current.value,
                },
                {},
                (data: any) => {
                  refTableRecipients.current.reload();
                }
              )
            }}
          >
            <span className='icon'><i className='fas fa-upload'></i></span>
            <span className='text'>{T.translate('Import emails')}</span>
          </button>
        </div>
      </div>
      <div className='card'>
        <div className='card-body'>
          <button
            className='btn btn-danger'
            onClick={() => {
              if (confirm('Are you sure to delete all recipients in this email?')) {
                request.post(
                  'email-marketing/api/remove-all-recipients',
                  {
                    idEmail: form.id                  },
                  {},
                  (data: any) => {
                    refTableRecipients.current.reload();
                  }
                );
              }
            }}
          >
            <span className='icon'><i className='fas fa-trash'></i></span>
            <span className='text'>{T.translate('Remove all recipients')}</span>
          </button>
        </div>
      </div>
    </div>
  </div>;
}

/** TabTest */
const TabTest = ({ formEmail }) => {
  const form = React.useContext(FormMetaContext);
  const refTestRecipientInput: any = React.createRef();

  const [testEmailVariables, setTestEmailVariables] = useState([]);
  const [testEmailSendResult, setTestEmailSendResult] = useState(null);
  const [emailTestInfo, setEmailTestInfo] = useState(null);
  const [recentlyContactedPeriod, setRecentlyContactedPeriod] = useState(1);

  const updateEmailTestInfo = () => {
    request.post(
      'email-marketing/api/get-email-test-info',
      {
        idEmail: form.id,
        recentlyContactedPeriod: recentlyContactedPeriod,
      },
      {},
      (data: any) => {
        setEmailTestInfo(data);
      }
    );
  }

  if (!emailTestInfo) updateEmailTestInfo();

  return <div className='flex gap-2'>
    <div className='card flex-1'>
      <div className='card-header'>{T.translate('Analysis & warnings')}</div>
      <div className='card-body'>
        {emailTestInfo ? <>
          {emailTestInfo.warnings.length == 0 ? 
            <div className='alert alert-success'>
              <i className='fas fa-check mr-2'></i>
              {T.translate('No warnings')}
            </div>
          :
            emailTestInfo.warnings.map((item, key) => {
              return <div key={key} className='alert alert-warning'>{item}</div>;
            })
          }
          <div className='flex gap-1 mt-2'>
            <b className='flex gap-1 items-center'>Recipients contacted in last <input
              type='number'
              className='w-12'
              value={recentlyContactedPeriod}
              onChange={(event: any) => {
                setRecentlyContactedPeriod(event.target.value);
                updateEmailTestInfo();
              }}
            ></input> months</b>
            <button
              className='btn btn-delete-outline btn-small'
              onClick={() => {
                let emails = [];
                Object.keys(emailTestInfo.recentlyContacted).map((email, key) => {
                  emails.push(email)
                });
                formEmail.removeRecipients(emails);
              }}
            >
              <span className='icon'><i className='fas fa-trash-can'></i></span>
              <span className='text'>Remove all</span>
            </button>
          </div>
          {Object.keys(emailTestInfo.recentlyContacted).length == 0
            ? <div className='flex gap-1 items-center'>No recipients found.</div>
            : <table className='table-default dense'>
              <thead>
                <tr>
                  <th>#</th>
                  <th>{T.translate('Email')}</th>
                  <th>{T.translate('When')}</th>
                  <th>{T.translate('Email')}</th>
                  {/* <th></th> */}
                </tr>
              </thead>
              <tbody>
                {Object.keys(emailTestInfo.recentlyContacted).map((email, key) => {
                  const details = emailTestInfo.recentlyContacted[email];
                  return <tr>
                    <td className='text-nowrap'>{key+1}</td>
                    <td className='text-nowrap'>{email}</td>
                    <td className='text-nowrap'>{details.mailSent}</td>
                    <td className='text-nowrap'>{details.emailName}</td>
                    <td>
                      <button
                        className='btn btn-delete-outline btn-small'
                        onClick={() => {
                          formEmail.removeRecipient(email);
                        }}
                      >
                        <span className='icon'><i className='fas fa-trash-can'></i></span>
                      </button>
                    </td>
                  </tr>;
                })}
              </tbody>
            </table>
          }
        </> : <div className='alert alert-warning'><Spinner></Spinner> {T.translate('Analysing email...')}</div>}
      </div>
    </div>
    <div className='card flex-1'>
      <div className='card-header'>{T.translate('Send test email')}</div>
      <div className='card-body'>
        {T.translate('Test email recipient:')}
        <input
          ref={refTestRecipientInput}
          className="ml-2"
          type="text"
          placeholder={T.translate("Recipient email")}
        />
        <br/>
        {T.translate("Test email variables:")}
        <InputJsonKeyValue uid="test-email-variables"
          onChange={(input: any, value: any) => {
            // input.setState({value: value});
            setTestEmailVariables(value);
          }}
        ></InputJsonKeyValue>
        <button
          className="btn btn-transparent mt-2"
          onClick={() => {
            request.post(
              'email-marketing/api/send-test-email',
              {
                idEmail: form.id,
                to: refTestRecipientInput.current.value,
                variables: testEmailVariables,
              },
              {},
              (result: any) => {
                setTestEmailSendResult(result);
              }
            );
          }}
        >
          <span className="icon"><i className="fas fa-envelope"></i></span>
          <span className="text">{T.translate('Send test email')}</span>
        </button>
        {testEmailSendResult && testEmailSendResult.status == 'success' ?
          <div className='alert alert-success mt-2'>{T.translate('Test email was sent to you.')}</div>
        : null}
        {testEmailSendResult && testEmailSendResult.status != 'success' ?
          <div className='alert alert-danger mt-2'>
            {T.translate('Error occured when sending a test email to you.')}
            <br/>
            <b>{testEmailSendResult.message}</b>
          </div>
        : null}
      </div>
    </div>
  </div>;
}

/** TabLaunch */
const TabLaunch = () => {
  const form = React.useContext(FormMetaContext);

  const idLaunchedBy: number = useRecordField('id_launched_by');
  const datetimeLaunched: any = useRecordField('datetime_launched');
  const LAUNCHED_BY: any = useRecordField('LAUNCHED_BY');

  const [emailLaunchInfo, setEmailLaunchInfo] = useState(null);
  const [launchResult, setLaunchResult] = useState(null);
  const [recentlyContactedPeriod, setRecentlyContactedPeriod] = useState(1);

  const updateEmailLaunchInfo = () => {
    setEmailLaunchInfo(null);
    request.post(
      'email-marketing/api/get-email-launch-info',
      {
        idEmail: form.id,
        recentlyContactedPeriod: recentlyContactedPeriod,
      },
      {},
      (data: any) => {
        setEmailLaunchInfo(data);
      }
    );

  }

  let invalidRecipientsCount = 0;
  let unsubscribedRecipientsCount = 0;
  let emailsSent = 0;
  let potentialLeads = [];

  if (emailLaunchInfo?.recipients) {
    emailLaunchInfo.recipients.map((item, key) => {
      if (item.STATUS?.is_unsubscribed) unsubscribedRecipientsCount++;
      if (item.STATUS?.is_invalid) invalidRecipientsCount++;
      if (item.MAIL?.datetime_sent) emailsSent++;

      let isPotentialLead = false;
      if (item.CLICK_GROUPS) {
        Object.keys(item.CLICK_GROUPS).map((ts, key) => {
          const group = item.CLICK_GROUPS[ts];
          if (
            group[0] <= 1 // clicks
            && group[1] == 0 // bot score
          ) {
            isPotentialLead = true;
          }
        });
      }

      if (isPotentialLead) potentialLeads.push(item.email);
    });
  }

  return <>
    {emailLaunchInfo ? <>
      {emailLaunchInfo.recentlyContacted
        && emailLaunchInfo.recentlyContacted.length > 0 ? <div className='alert alert-warning'>
        <b>{T.translate('Recently contacted')}</b>
        {emailLaunchInfo.recentlyContacted.map((item, key) => {
          if (!item.CONTACT) return null;
          return <div key={key}>
            <code>
              {item.CONTACT.first_name}&nbsp;{item.CONTACT.last_name}
              &nbsp;
              {item.CONTACT.VALUES ? item.CONTACT.VALUES.map((vItem, vKey) => {
                if (vItem.type == 'email') {
                  return <span key={vKey}>{vItem.value}</span>;
                } else {
                  return null;
                }
              }) : null}
            </code> {T.translate('in email')} <a
              href={globalThis.hubleto.config.projectUrl + '/email-marketing/emails/' + item.EMAIL.id}
              target='_blank'
            >{item.EMAIL.subject}</a>.
          </div>;
        })}
      </div> : null}
    </> : null}

    {idLaunchedBy ?
      <div className='alert alert-warning'>
        {T.translate(
          'Email was already launched by {{ email }} on {{ datetime }}.',
          null,
          null,
          {email: LAUNCHED_BY.email, datetime: datetimeLaunched}
        )}</div>
    : null}

    <div className='flex flex-col md:flex-row gap-2 w-full'>
      <div className=''>
        <button
          className="btn btn-add-outline btn-large"
          onClick={() => {
            request.post(
              'email-marketing/api/launch',
              { idEmail: form.id },
              {},
              (result: any) => {
                setLaunchResult(result);
              }
            );
          }}
        >
          <span className="icon"><i className="fas fa-paper-plane"></i></span>
          <span className="text">{T.translate('Send email now!')}</span>
        </button>
        <div className='mt-2 alert alert-info'>
          {T.translate('Emails will be sent only to recipients who did not receive email yet.')}
        </div>
        <div className='mt-2 alert alert-info'>
          {T.translate('Unsubscribed and invalid recipients will be ignored.')}
        </div>
        {emailLaunchInfo && emailLaunchInfo.recipients ? <>
          <div className='card mt-2'>
            <div className='card-header'>{T.translate('Statistics')}</div>
            <div className='card-body flex flex-col gap-1'>
              <div className='badge'>
                {T.translate('Recipients')}: {emailLaunchInfo.recipients.length}
              </div>
              <div className='badge'>
                {T.translate('Emails sent')}: {emailsSent}
              </div>
              <div className='badge badge-warning'>
                {T.translate('Invalid recipients')}: {invalidRecipientsCount} ({Math.round(invalidRecipientsCount / emailLaunchInfo.recipients.length * 100)} %)
              </div>
              <div className='badge badge-danger'>
                {T.translate('Unsubscribed recipients')}: {unsubscribedRecipientsCount} ({Math.round(unsubscribedRecipientsCount / emailLaunchInfo.recipients.length * 100)} %)
              </div>
            </div>
          </div>
          <div className='card mt-2'>
            <div className='card-header'>{T.translate('Potential leads')}</div>
            <div className='card-body flex flex-wrap gap-1'>
              {potentialLeads.map((email, key) => {
                return <div key={key} className='badge'>{email}</div>;
              })}
            </div>
          </div>
        </> : null}

        {launchResult && launchResult.status == 'success' ?
          <div className='alert alert-success mt-2'>{T.translate('Email was sent.')}</div>
        : null}
        {launchResult && launchResult.status != 'success' ?
          <div className='alert alert-danger mt-2'>
            {T.translate('Error occured when launching the email.')}<br/>
            <b>{launchResult.message}</b>
          </div>
        : null}
      </div>
      <div className='card grow'>
        <div className='card-header'>{T.translate('Recipients')}</div>
        <div className='card-body'>
          {emailLaunchInfo && emailLaunchInfo.recipients ? 
            <table className='table-default dense'>
              <thead>
                <tr>
                  <th rowSpan={2}>#</th>
                  <th rowSpan={2}>{T.translate('Email')}</th>
                  <th rowSpan={2}>{T.translate('Status')}</th>
                  <th rowSpan={2}>{T.translate('Clicks')}</th>
                  <th colSpan={2}>{T.translate('Bot score')}</th>
                </tr>
                <tr>
                  <th>{T.translate('Total')}</th>
                  <th>{T.translate('Details')}</th>
                </tr>
              </thead>
              <tbody>
                {emailLaunchInfo.recipients.map((item, key) => {
                  let botScoreTotal = 0;
                  item.CLICKS.map((click, key) => {
                    botScoreTotal += click.bot_score;
                  });

                  let recentlyContacted = emailLaunchInfo.recentlyContacted[item.email];

                  return <tr>
                    <td className='text-nowrap'>{key+1}</td>
                    <td className={'text-nowrap' + (item.CLICKS.length > 0 ? ' bg-green-100' : '')}>{item.email}</td>
                    <td className='text-nowrap'>
                      {item.id_mail > 0 ? <>
                        {item.MAIL?.datetime_sent
                          ? <div className='badge badge-success'>{T.translate('Sent')} {item.MAIL?.datetime_sent}</div>
                          : <div className='badge badge-warning'>{T.translate('Scheduled')} {item.MAIL?.datetime_scheduled_to_send}</div>
                        }
                      </> : <div className='badge'>{T.translate('Not scheduled yet')}</div>}
                      {item.STATUS?.is_unsubscribed ? <div className='badge badge-danger'>{T.translate('Unsubscribed')}</div> : null}
                      {item.STATUS?.is_invalid ? <div className='badge badge-warning'>{T.translate('Invalid')}</div> : null}
                      {recentlyContacted ? <div className='badge'>
                        Contacted {recentlyContacted.mailSent} in <i>{recentlyContacted.emailName}</i>
                      </div> : null}
                    </td>
                    <td>
                      {item.CLICKS.length > 0 ? item.CLICKS.length : null}
                    </td>
                    <td className={'text-nowrap text-red-800'}>{botScoreTotal > 0 ? botScoreTotal : null}</td>
                    <td className='text-nowrap'>
                      {item.CLICK_GROUPS ? Object.keys(item.CLICK_GROUPS).map((ts, key) => {
                        const group = item.CLICK_GROUPS[ts];
                        return <div key={key} className='text-xs'>
                          #{key+1} {moment.unix(parseInt(ts)).format("YYYY-MM-DD HH:mm:ss")} = {group[0]}, {group[1]}
                        </div>;
                      }) : null}
                    </td>
                  </tr>
                })}
              </tbody>
            </table>
          : <div className='alert alert-warning'>{T.translate('Loading information about recipients and launch status.')}</div>}
        </div>
      </div>
    </div>
  </>;
}

/** TabClicks */
const TabClicks = () => {
  const form = React.useContext(FormMetaContext);

  return <TableEmailClicks
    parentForm={form}
    tag="table_email_click"
    uid={form.uid + "_table_email_click"}
    idEmail={form.id}
  />;
}

/** FormEmail */
const FormEmail = (props: FormEmailProps) => {

  const [recipients, setRecipients] = useState([]);
  const [subTab, setSubTab] = useState('');

  const removeRecipient = (email: string) => {
    request.post(
      'email-marketing/api/remove-recipient-from-email',
      {
        idEmail: props.id,
        email: email,
      },
      {},
      (data: any) => {}
    );
  }

  const removeRecipients = (emails: Array<string>) => {
    request.post(
      'email-marketing/api/remove-recipient-from-email',
      {
        idEmail: props.id,
        emails: emails,
      },
      {},
      (data: any) => {}
    );
  }

  const formEmail = {
    props,
    recipients, setRecipients,
    subTab, setSubTab,

    removeRecipient, removeRecipients,
  };

  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Email'}
    urlSlug='email-marketing/emails'
    // onTabChange={(form: any) => {
    //   switch (form.activeTabUid) {
    //     case 'test':
    //       updateEmailTestInfo();
    //     break;
    //     case 'launch':
    //       updateEmailLaunchInfo();
    //     break;
    //   }
    // }}
    title={{field: 'title', sub: T.translate('Email')}}
    tabs={{
      default: { title: <b>{T.translate('Email')}</b>, content: () => <TabDefault /> },
      contacts: { title: T.translate('Contacts'), content: () => <TabContacts /> },
      recipients: { title: T.translate('Recipients'), content: () => <TabRecipients formEmail={formEmail} /> },
      test: { title: T.translate('Test'), content: () => <TabTest formEmail={formEmail} /> },
      launch: { title: T.translate('Launch'), content: () => <TabLaunch /> },
      clicks: { title: T.translate('Clicks'), content: () => <TabClicks /> },
    }}
    {...props}
  ></Form>
}

export default FormEmail;
