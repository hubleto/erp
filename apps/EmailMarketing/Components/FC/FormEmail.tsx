import React, { useState } from 'react';
import TableContacts from '@hubleto/apps/Contacts/Components/TableContacts';
import TableRecipients from './TableRecipients';
import TableEmailClicks from '@hubleto/apps/EmailMarketing/Components/TableEmailClicks';
import request from '@hubleto/react-ui/core/Request';
import InputJsonKeyValue from "@hubleto/react-ui/components/cc/Inputs/JsonKeyValue";
import moment from "moment";
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Translator from '@hubleto/react-ui/core/Translator';
import { useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormEmailProps extends FormProps {}

const translate = new Translator(
  'Hubleto\\App\\Community\\EmailMarketing\\Loader',
  'Components\\FormEmail'
).translate;

/**
 * Title
 *
 * @var [type]
 */
const Title = (props: FormEmailProps) => <>
  <small>{translate('Email')}</small>
  <h2>{useRecordField('name') ?? '-'}</h2>
</>;

/**
 * TabDefault
 *
 * @var [type]
 */
const TabDefault = ({ formEmail }) => {
  return <>
    <div className='w-full flex flex-col md:flex-row gap-2'>
      <div className='flex-4 border-r border-gray-100'>
        <Input field='title' />
        <Input field='id_sender_account' />
        <Input field='reply_to' />
        <Input field='mail_subject' />
        <Input field='mail_body' />
      </div>
      <div className='flex-1'>
        <Input field='is_approved' />
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

/**
 * TabContacts
 *
 * @var [type]
 */
const TabContacts = ({ formEmail }) => {
  const form = React.useContext(FormMetaContext);
  const RECIPIENTS: Array<any> = useRecordField('RECIPIENTS');

  return <div>
    <div>
      { translate('Select contacts which will be added as recipients') }
    </div>
    <TableContacts
      tag={"table_email_contact"}
      parentForm={this}
      uid={formEmail.props.uid + "_table_email_contact"}
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
            idEmail: formEmail.props.id,
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

/**
 * TabRecipients
 *
 * @var [type]
 */
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
        uid={formEmail.props.uid + "_table_email_recipient"}
        idEmail={formEmail.props.id}
        view='briefOverview'
        onAfterLoadData={(table: any) => {
          formEmail.setRecipients(table.state.data.records);
        }}
      />
    </div>
    <div className='flex-1 gap-2'>
      <div className='card'>
        <div className='card-header'>{translate('Import emails')}</div>
        <div className='card-body'>
          <textarea
            className='w-full h-80'
            placeholder={translate('One email per line.')}
            ref={refEmails}
          ></textarea>
          <button
            className='btn btn-add-outline mt-2 w-full'
            onClick={() => {
              request.post(
                'email-marketing/api/import-recipients',
                {
                  idEmail: formEmail.props.id,
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
            <span className='text'>{translate('Import emails')}</span>
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
                    idEmail: formEmail.props.id                  },
                  {},
                  (data: any) => {
                    refTableRecipients.current.reload();
                  }
                );
              }
            }}
          >
            <span className='icon'><i className='fas fa-trash'></i></span>
            <span className='text'>{translate('Remove all recipients')}</span>
          </button>
        </div>
      </div>
    </div>
  </div>;
}

/**
 * TabTest
 *
 * @var [type]
 */
const TabTest = ({ formEmail }) => {
  const refTestRecipientInput: any = React.createRef();

  return <div className='flex gap-2'>
    <div className='card flex-1'>
      <div className='card-header'>{ translate('Analysis & warnings') }</div>
      <div className='card-body'>
        {formEmail.emailTestInfo ? <>
          {formEmail.emailTestInfo.warnings.length == 0 ? 
            <div className='alert alert-success'>
              <i className='fas fa-check mr-2'></i>
              { translate('No warnings') }
            </div>
          :
            formEmail.emailTestInfo.warnings.map((item, key) => {
              return <div key={key} className='alert alert-warning'>{item}</div>;
            })
          }
          <div className='flex gap-1'>
            <b className='flex gap-1 items-center'>Recipients contacted in last <input
              type='number'
              className='w-12'
              value={formEmail.recentlyContactedPeriod}
              onChange={(event: any) => {
                formEmail.setRecentlyContactedPeriod(event.target.value);
                formEmail.updateEmailTestInfo();
              }}
            ></input> months</b>
            <button
              className='btn btn-delete-outline btn-small'
              onClick={() => {
                let emails = [];
                Object.keys(formEmail.emailTestInfo.recentlyContacted).map((email, key) => {
                  emails.push(email)
                });
                formEmail.removeRecipients(emails);
              }}
            >
              <span className='icon'><i className='fas fa-trash-can'></i></span>
              <span className='text'>Remove all</span>
            </button>
          </div>
          {Object.keys(formEmail.emailTestInfo.recentlyContacted).length == 0
            ? <div className='flex gap-1 items-center'>No recipients found.</div>
            : <table className='table-default dense'>
              <thead>
                <tr>
                  <th>#</th>
                  <th>{translate('Email')}</th>
                  <th>{translate('When')}</th>
                  <th>{translate('Email')}</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                {Object.keys(formEmail.emailTestInfo.recentlyContacted).map((email, key) => {
                  const details = formEmail.emailTestInfo.recentlyContacted[email];
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
        </> : <div className='alert alert-warning'>{ translate('Analysing email...') }</div>}
      </div>
    </div>
    <div className='card flex-1'>
      <div className='card-header'>{ translate('Send test email') }</div>
      <div className='card-body'>
        { translate('Test email recipient:') }
        <input
          ref={refTestRecipientInput}
          className="ml-2"
          type="text"
          placeholder={ translate("Recipient email") }
        />
        <br/>
        { translate("Test email variables:") }
        <InputJsonKeyValue uid="test-email-variables"
          onChange={(input: any, value: any) => {
            // input.setState({value: value});
            formEmail.setTestEmailVariables(value);
          }}
        ></InputJsonKeyValue>
        <button
          className="btn btn-transparent mt-2"
          onClick={() => {
            request.post(
              'email-marketing/api/send-test-email',
              {
                idEmail: formEmail.props.id,
                to: refTestRecipientInput.current.value,
                variables: formEmail.testEmailVariables,
              },
              {},
              (result: any) => {
                formEmail.setTestEmailSendResult(result);
              }
            );
          }}
        >
          <span className="icon"><i className="fas fa-envelope"></i></span>
          <span className="text">{translate('Send test email')}</span>
        </button>
        {formEmail.testEmailSendResult && formEmail.testEmailSendResult.status == 'success' ?
          <div className='alert alert-success mt-2'>{translate('Test email was sent to you.')}</div>
        : null}
        {formEmail.testEmailSendResult && formEmail.testEmailSendResult.status != 'success' ?
          <div className='alert alert-danger mt-2'>
            { translate('Error occured when sending a test email to you.') }
            <br/>
            <b>{formEmail.testEmailSendResult.message}</b>
          </div>
        : null}
      </div>
    </div>
  </div>;
}

/**
 * TabLaunch
 *
 * @var [type]
 */
const TabLaunch = ({ formEmail }) => {
  const idLaunchedBy: number = useRecordField('id_launched_by');
  const datetimeLaunched: any = useRecordField('datetime_launched');
  const LAUNCHED_BY: any = useRecordField('LAUNCHED_BY');

  let invalidRecipientsCount = 0;
  let unsubscribedRecipientsCount = 0;
  let emailsSent = 0;
  let potentialLeads = [];

  if (formEmail.emailLaunchInfo?.recipients) {
    formEmail.emailLaunchInfo.recipients.map((item, key) => {
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
    {formEmail.emailLaunchInfo ? <>
      {formEmail.emailLaunchInfo.recentlyContacted
        && formEmail.emailLaunchInfo.recentlyContacted.length > 0 ? <div className='alert alert-warning'>
        <b>{translate('Recently contacted')}</b>
        {formEmail.emailLaunchInfo.recentlyContacted.map((item, key) => {
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
            </code> {translate('in email')} <a
              href={globalThis.hubleto.config.projectUrl + '/email-marketing/emails/' + item.EMAIL.id}
              target='_blank'
            >{item.EMAIL.subject}</a>.
          </div>;
        })}
      </div> : null}
    </> : null}

    {idLaunchedBy ?
      <div className='alert alert-warning'>
        {translate(
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
              { idEmail: formEmail.props.id },
              {},
              (result: any) => {
                formEmail.setLaunchResult(result);
              }
            );
          }}
        >
          <span className="icon"><i className="fas fa-paper-plane"></i></span>
          <span className="text">{translate('Send email now!')}</span>
        </button>
        <div className='mt-2 alert alert-info'>
          {translate('Emails will be sent only to recipients who did not receive email yet.')}
        </div>
        <div className='mt-2 alert alert-info'>
          {translate('Unsubscribed and invalid recipients will be ignored.')}
        </div>
        {formEmail.emailLaunchInfo && formEmail.emailLaunchInfo.recipients ? <>
          <div className='card mt-2'>
            <div className='card-header'>{translate('Statistics')}</div>
            <div className='card-body flex flex-col gap-1'>
              <div className='badge'>
                {translate('Recipients')}: {formEmail.emailLaunchInfo.recipients.length}
              </div>
              <div className='badge'>
                {translate('Emails sent')}: {emailsSent}
              </div>
              <div className='badge badge-warning'>
                {translate('Invalid recipients')}: {invalidRecipientsCount} ({Math.round(invalidRecipientsCount / formEmail.emailLaunchInfo.recipients.length * 100)} %)
              </div>
              <div className='badge badge-danger'>
                {translate('Unsubscribed recipients')}: {unsubscribedRecipientsCount} ({Math.round(unsubscribedRecipientsCount / formEmail.emailLaunchInfo.recipients.length * 100)} %)
              </div>
            </div>
          </div>
          <div className='card mt-2'>
            <div className='card-header'>{translate('Potential leads')}</div>
            <div className='card-body flex flex-wrap gap-1'>
              {potentialLeads.map((email, key) => {
                return <div key={key} className='badge'>{email}</div>;
              })}
            </div>
          </div>
        </> : null}

        {formEmail.launchResult && formEmail.launchResult.status == 'success' ?
          <div className='alert alert-success mt-2'>{translate('Email was sent.')}</div>
        : null}
        {formEmail.launchResult && formEmail.launchResult.status != 'success' ?
          <div className='alert alert-danger mt-2'>
            {translate('Error occured when launching the email.')}<br/>
            <b>{formEmail.launchResult.message}</b>
          </div>
        : null}
      </div>
      <div className='card grow'>
        <div className='card-header'>{translate('Recipients')}</div>
        <div className='card-body'>
          {formEmail.emailLaunchInfo && formEmail.emailLaunchInfo.recipients ? 
            <table className='table-default dense'>
              <thead>
                <tr>
                  <th rowSpan={2}>#</th>
                  <th rowSpan={2}>{translate('Email')}</th>
                  <th rowSpan={2}>{translate('Status')}</th>
                  <th rowSpan={2}>{translate('Clicks')}</th>
                  <th colSpan={2}>{translate('Bot score')}</th>
                </tr>
                <tr>
                  <th>{translate('Total')}</th>
                  <th>{translate('Details')}</th>
                </tr>
              </thead>
              <tbody>
                {formEmail.emailLaunchInfo.recipients.map((item, key) => {
                  let botScoreTotal = 0;
                  item.CLICKS.map((click, key) => {
                    botScoreTotal += click.bot_score;
                  });

                  let recentlyContacted = formEmail.emailLaunchInfo.recentlyContacted[item.email];

                  return <tr>
                    <td className='text-nowrap'>{key+1}</td>
                    <td className={'text-nowrap' + (item.CLICKS.length > 0 ? ' bg-green-100' : '')}>{item.email}</td>
                    <td className='text-nowrap'>
                      {item.id_mail > 0 ? <>
                        {item.MAIL?.datetime_sent
                          ? <div className='badge badge-success'>{translate('Sent')} {item.MAIL?.datetime_sent}</div>
                          : <div className='badge badge-warning'>{translate('Scheduled')} {item.MAIL?.datetime_scheduled_to_send}</div>
                        }
                      </> : <div className='badge'>{translate('Not scheduled yet')}</div>}
                      {item.STATUS?.is_unsubscribed ? <div className='badge badge-danger'>{translate('Unsubscribed')}</div> : null}
                      {item.STATUS?.is_invalid ? <div className='badge badge-warning'>{translate('Invalid')}</div> : null}
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
          : <div className='alert alert-warning'>{translate('Loading information about recipients and launch status.')}</div>}
        </div>
      </div>
    </div>
  </>;
}

const TabClicks = ({ formEmail }) => {
  return <TableEmailClicks
    parentForm={this}
    tag="table_email_click"
    uid={formEmail.uid + "_table_email_click"}
    idEmail={formEmail.props.id}
  />;
}

/**
 * FormEmail
 *
 * @var [type]
 */
const FormEmail = (props: FormEmailProps) => {

  const [testEmailVariables, setTestEmailVariables] = useState([]);
  const [testEmailSendResult, setTestEmailSendResult] = useState(null);
  const [launchResult, setLaunchResult] = useState(null);
  const [emailTestInfo, setEmailTestInfo] = useState(null);
  const [emailLaunchInfo, setEmailLaunchInfo] = useState(null);
  const [recipients, setRecipients] = useState([]);
  const [recentlyContactedPeriod, setRecentlyContactedPeriod] = useState(1);
  const [subTab, setSubTab] = useState('');

  const removeRecipient = (email: string) => {
    setEmailTestInfo(null)
    request.post(
      'email-marketing/api/remove-recipient-from-email',
      {
        idEmail: props.id,
        email: email,
      },
      {},
      (data: any) => {
        updateEmailTestInfo();
      }
    );
  }

  const removeRecipients = (emails: Array<string>) => {
    setEmailTestInfo(null);
    request.post(
      'email-marketing/api/remove-recipient-from-email',
      {
        idEmail: props.id,
        emails: emails,
      },
      {},
      (data: any) => {
        updateEmailTestInfo();
      }
    );
  }

  const updateEmailTestInfo = () => {
    setEmailTestInfo(null);
    request.post(
      'email-marketing/api/get-email-test-info',
      {
        idEmail: props.id,
        recentlyContactedPeriod: recentlyContactedPeriod,
      },
      {},
      (data: any) => {
        setEmailTestInfo(data);
      }
    );
  }

  const updateEmailLaunchInfo = () => {
    setEmailLaunchInfo(null);
    request.post(
      'email-marketing/api/get-email-launch-info',
      {
        idEmail: props.id,
        recentlyContactedPeriod: recentlyContactedPeriod,
      },
      {},
      (data: any) => {
        setEmailLaunchInfo(data);
      }
    );

  }

  const formEmail = {
    props,
    testEmailVariables, setTestEmailVariables,
    testEmailSendResult, setTestEmailSendResult,
    launchResult, setLaunchResult,
    emailTestInfo, setEmailTestInfo,
    emailLaunchInfo, setEmailLaunchInfo,
    recipients, setRecipients,
    recentlyContactedPeriod, setRecentlyContactedPeriod,
    subTab, setSubTab,

    removeRecipient, removeRecipients,
    updateEmailTestInfo, updateEmailLaunchInfo
  };

  return <Form
    componentName='FormEmail'
    parentApp='Hubleto/App/Community/EmailMarketing'
    model='Hubleto/App/Community/EmailMarketing/Models/Email'
    urlSlug='email-marketing/emails'
    onTabChange={(form: any) => {
      switch (form.activeTabUid) {
        case 'test':
          updateEmailTestInfo();
        break;
        case 'launch':
          updateEmailLaunchInfo();
        break;
      }
    }}
    uiComponents={{
      title: <Title {...props} />,
      tabs: {
        default: { title: <b>{translate('Email')}</b>, content: () => <TabDefault formEmail={formEmail} /> },
        contacts: { title: translate('Contacts'), content: () => <TabContacts formEmail={formEmail} /> },
        recipients: { title: translate('Recipients'), content: () => <TabRecipients formEmail={formEmail} /> },
        test: { title: translate('Test'), content: () => <TabTest formEmail={formEmail} /> },
        launch: { title: translate('Launch'), content: () => <TabLaunch formEmail={formEmail} /> },
        clicks: { title: translate('Clicks'), content: () => <TabClicks formEmail={formEmail} /> },
      },
    }}
    {...props}
  ></Form>
}

export default FormEmail;

//   parentApp: string = 'Hubleto/App/Community/EmailMarketing';

//   refTestRecipientInput: any = React.createRef();
//   refLogActivityInput: any = React.createRef();
//   refActivityModal: any = React.createRef();
//   refActivityForm: any = React.createRef();
//   refEmails: any = React.createRef();
//   refTableRecipients: any = React.createRef();



//   onTabChange() {
//     super.onTabChange();


//   renderTab(tabUid: string) {
//     const R = this.state.record;

//     switch (tabUid) {
//       case 'default':
//       break

//       case 'contacts':
//       break;

//       case 'test':
//       break;

//       case 'launch':
//       break;

//       case 'clicks':
//       break;

//       case 'recipients':
//       break;

//       default:
//         return super.renderTab(tabUid);
//       break;
//     }
//   }
// }

