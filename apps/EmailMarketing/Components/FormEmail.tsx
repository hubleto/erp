import React, { Component, ChangeEvent } from 'react';
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import TableContacts from '@hubleto/apps/Contacts/Components/TableContacts';
import TableRecipients from '@hubleto/apps/EmailMarketing/Components/TableRecipients';
import TableEmailClicks from '@hubleto/apps/EmailMarketing/Components/TableEmailClicks';
import request from '@hubleto/react-ui/core/Request';
import InputJsonKeyValue from "@hubleto/react-ui/core/Inputs/JsonKeyValue";
import moment, { Moment } from "moment";
import { updateFormWorkflowByTag } from '@hubleto/react-ui/ext/ErpWorkflowSelector';

export interface FormEmailProps extends FormExtendedProps {}
export interface FormEmailState extends FormExtendedState {
  testEmailVariables?: any,
  testEmailSendResult?: any,
  launchResult?: any,
  emailTestInfo?: any,
  emailLaunchInfo?: any,
  recipients?: any,
  showIdActivity: number,
  activityTime: string,
  activityDate: string,
  activitySubject: string,
  activityAllDay: boolean,
  recentlyContactedPeriod?: number,
  subTab: string,
}

export default class FormEmail<P, S> extends FormExtended<FormEmailProps, FormEmailState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/EmailMarketing/Models/Email',
    renderWorkflowUi: true,
    renderOwnerManagerUi: true,
  };

  props: FormEmailProps;
  state: FormEmailState;

  translationContext: string = 'Hubleto\\App\\Community\\EmailMarketing\\Loader';
  translationContextInner: string = 'Components\\FormEmail';

  parentApp: string = 'Hubleto/App/Community/EmailMarketing';

  refTestRecipientInput: any = React.createRef();
  refLogActivityInput: any = React.createRef();
  refActivityModal: any = React.createRef();
  refActivityForm: any = React.createRef();
  refEmails: any = React.createRef();
  refTableRecipients: any = React.createRef();

  constructor(props: FormEmailProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormEmailProps) {
    return {
      ...super.getStateFromProps(props),
      showIdActivity: 0,
      activityTime: '',
      activityDate: '',
      activitySubject: '',
      activityAllDay: false,
      emailTestInfo: null,
      emailLaunchInfo: null,
      recentlyContactedPeriod: 1,
      tabs: [
        { uid: 'default', title: <b>{this.translate('Email')}</b> },
        { uid: 'contacts', title: this.translate('Contacts') },
        { uid: 'recipients', title: this.translate('Recipients') },
        { uid: 'test', title: this.translate('Test') },
        { uid: 'launch', title: this.translate('Launch') },
        { uid: 'clicks', title: this.translate('Clicks') },
        ...this.getCustomTabs()
      ],
      subTab: '',
    };
  }

  getRecordFormUrl(): string {
    return 'email-marketing/emails/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  contentClassName(): string
  {
    return this.state.record.is_closed ? 'bg-slate-100' : '';
  }

  removeRecipient(email: string) {
    this.setState({emailTestInfo: null}, () => {
      request.post(
        'email-marketing/api/remove-recipient-from-email',
        {
          idEmail: this.state.record.id,
          email: email,
        },
        {},
        (data: any) => {
          this.updateEmailTestInfo();
        }
      );
    });
  }

  removeRecipients(emails: Array<string>) {
    this.setState({emailTestInfo: null}, () => {
      request.post(
        'email-marketing/api/remove-recipient-from-email',
        {
          idEmail: this.state.record.id,
          emails: emails,
        },
        {},
        (data: any) => {
          this.updateEmailTestInfo();
        }
      );
    });
  }

  updateEmailTestInfo() {
    this.setState({emailTestInfo: null}, () => {
      request.post(
        'email-marketing/api/get-email-test-info',
        {
          idEmail: this.state.record.id,
          recentlyContactedPeriod: this.state.recentlyContactedPeriod,
        },
        {},
        (data: any) => {
          this.setState({emailTestInfo: data});
        }
      );
    });
  }

  updateEmailLaunchInfo() {
    this.setState({emailLaunchInfo: null}, () => {
      request.post(
        'email-marketing/api/get-email-launch-info',
        {
          idEmail: this.state.record.id,
          recentlyContactedPeriod: this.state.recentlyContactedPeriod,
        },
        {},
        (data: any) => {
          this.setState({emailLaunchInfo: data});
        }
      );
    });

  }

  onTabChange() {
    super.onTabChange();

    const tabUid = this.state.activeTabUid;
    switch (tabUid) {
      case 'test':
        this.updateEmailTestInfo();
      break;
      case 'launch':
        this.updateEmailLaunchInfo();
      break;
    }
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate("Email")}</small>
      <h2>{this.state.record.name ?? '-'}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <>
          <div className='w-full flex flex-col md:flex-row gap-2'>
            <div className='flex-4 border-r border-gray-100'>
              {this.inputWrapper('title')}
              {this.inputWrapper('id_sender_account')}
              {this.inputWrapper('reply_to')}
              {this.inputWrapper('mail_subject')}
              {this.inputWrapper('mail_body')}
            </div>
            <div className='flex-1'>
              {this.inputWrapper('is_approved')}
              {this.inputWrapper('target_audience')}
              {this.inputWrapper('goal')}
              {this.inputWrapper('notes')}
              {this.inputWrapper('utm_source')}
              {this.inputWrapper('utm_campaign')}
              {this.inputWrapper('utm_term')}
              {this.inputWrapper('utm_content')}
              {this.inputWrapper('color')}
              {this.inputWrapper('datetime_created')}
              {this.inputWrapper('uid')}
            </div>
          </div>
        </>;
      break

      case 'contacts':
        return <div>
          <div>
            { this.translate('Select contacts which will be added as recipients') }
          </div>
          <TableContacts
            tag={"table_email_contact"}
            parentForm={this}
            uid={this.props.uid + "_table_email_contact"}
            selectionMode='multiple'
            readonly={true}
            descriptionSource='both'
            //@ts-ignore
            description={{ui: {showHeader: false}}}
            idCustomer={0}
            selection={R && R.RECIPIENTS ? R.RECIPIENTS.map((item) => { return { id: item.id_contact } }) : null}
            onSelectionChange={(table: any) => {
              request.post(
                'email-marketing/api/save-recipients-from-contacts',
                {
                  idEmail: this.state.record.id,
                  contactIds: table.state.selection.map((item) => item.id)
                },
                {},
                (result: any) => {
                  this.setState({record: {...R, RECIPIENTS: result}});
                }
              );
            }}
          />
        </div>;
      break;

      case 'test':
        return <div className='flex gap-2'>
          <div className='card flex-1'>
            <div className='card-header'>{ this.translate('Analysis & warnings') }</div>
            <div className='card-body'>
              {this.state.emailTestInfo ? <>
                {this.state.emailTestInfo.warnings.length == 0 ? 
                  <div className='alert alert-success'>
                    <i className='fas fa-check mr-2'></i>
                    { this.translate('No warnings') }
                  </div>
                :
                  this.state.emailTestInfo.warnings.map((item, key) => {
                    return <div key={key} className='alert alert-warning'>{item}</div>;
                  })
                }
                <div className='flex gap-1'>
                  <b className='flex gap-1 items-center'>Recipients contacted in last <input
                    type='number'
                    className='w-12'
                    value={this.state.recentlyContactedPeriod}
                    onChange={(event: ChangeEvent<HTMLInputElement>) => {
                      this.setState({recentlyContactedPeriod: event.target.value}, () => {
                        this.updateEmailTestInfo();
                      });
                    }}
                  ></input> months</b>
                  <button
                    className='btn btn-delete-outline btn-small'
                    onClick={() => {
                      let emails = [];
                      Object.keys(this.state.emailTestInfo.recentlyContacted).map((email, key) => {
                        emails.push(email)
                      });
                      this.removeRecipients(emails);
                    }}
                  >
                    <span className='icon'><i className='fas fa-trash-can'></i></span>
                    <span className='text'>Remove all</span>
                  </button>
                </div>
                {Object.keys(this.state.emailTestInfo.recentlyContacted).length == 0
                  ? <div className='flex gap-1 items-center'>No recipients found.</div>
                  : <table className='table-default dense'>
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>{ this.translate('Email') }</th>
                        <th>{ this.translate('When') }</th>
                        <th>{ this.translate('Email') }</th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody>
                      {Object.keys(this.state.emailTestInfo.recentlyContacted).map((email, key) => {
                        const details = this.state.emailTestInfo.recentlyContacted[email];
                        return <tr>
                          <td className='text-nowrap'>{key+1}</td>
                          <td className='text-nowrap'>{email}</td>
                          <td className='text-nowrap'>{details.mailSent}</td>
                          <td className='text-nowrap'>{details.emailName}</td>
                          <td>
                            <button
                              className='btn btn-delete-outline btn-small'
                              onClick={() => {
                                this.removeRecipient(email);
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
              </> : <div className='alert alert-warning'>{ this.translate('Analysing email...') }</div>}
            </div>
          </div>
          <div className='card flex-1'>
            <div className='card-header'>{ this.translate('Send test email') }</div>
            <div className='card-body'>
              { this.translate('Test email recipient:') }
              <input
                ref={this.refTestRecipientInput}
                className="ml-2"
                type="text"
                placeholder={ this.translate("Recipient email") }
              />
              <br/>
              { this.translate("Test email variables:") }
              <InputJsonKeyValue uid="test-email-variables"
                onChange={(input: any, value: any) => {
                  input.setState({value: value});
                  this.setState({testEmailVariables: value});
                }}
              ></InputJsonKeyValue>
              <button
                className="btn btn-transparent mt-2"
                onClick={() => {
                  request.post(
                    'email-marketing/api/send-test-email',
                    {
                      idEmail: this.state.record.id,
                      to: this.refTestRecipientInput.current.value,
                      variables: this.state.testEmailVariables,
                    },
                    {},
                    (result: any) => {
                      this.setState({testEmailSendResult: result})
                    }
                  );
                }}
              >
                <span className="icon"><i className="fas fa-envelope"></i></span>
                <span className="text">{this.translate('Send test email')}</span>
              </button>
              {this.state.testEmailSendResult && this.state.testEmailSendResult.status == 'success' ?
                <div className='alert alert-success mt-2'>{ this.translate('Test email was sent to you.') }</div>
              : null}
              {this.state.testEmailSendResult && this.state.testEmailSendResult.status != 'success' ?
                <div className='alert alert-danger mt-2'>
                  { this.translate('Error occured when sending a test email to you.') }
                  <br/>
                  <b>{this.state.testEmailSendResult.message}</b>
                </div>
              : null}
            </div>
          </div>
        </div>;
      break;

      case 'launch':
        let invalidRecipientsCount = 0;
        let unsubscribedRecipientsCount = 0;
        let emailsSent = 0;
        let potentialLeads = [];

        if (this.state.emailLaunchInfo?.recipients) {
          this.state.emailLaunchInfo.recipients.map((item, key) => {
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
          {this.state.emailLaunchInfo ? <>
            {this.state.emailLaunchInfo.recentlyContacted
              && this.state.emailLaunchInfo.recentlyContacted.length > 0 ? <div className='alert alert-warning'>
              <b>{this.translate('Recently contacted')}</b>
              {this.state.emailLaunchInfo.recentlyContacted.map((item, key) => {
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
                  </code> {this.translate('in email')} <a
                    href={globalThis.hubleto.config.projectUrl + '/email-marketing/emails/' + item.EMAIL.id}
                    target='_blank'
                  >{item.EMAIL.subject}</a>.
                </div>;
              })}
            </div> : null}
          </> : null}

          {R.id_launched_by ?
            <div className='alert alert-warning'>
              {this.translate(
                'Email was already launched by {{ email }} on {{ datetime }}.',
                this.translationContext,
                this.translationContextInner,
                {email: R.LAUNCHED_BY.email, datetime: R.datetime_launched}
              )}</div>
          : null}

          <div className='flex flex-col md:flex-row gap-2 w-full'>
            <div className=''>
              <button
                className="btn btn-add-outline btn-large"
                onClick={() => {
                  request.post(
                    'email-marketing/api/launch',
                    { idEmail: this.state.record.id },
                    {},
                    (result: any) => {
                      this.setState({launchResult: result});
                      if (result.status && result.status == 'success') {
                        updateFormWorkflowByTag(this, 'email-marketing-launched', () => {
                          this.saveRecord();
                          this.reload();
                        });
                      }
                    }
                  );
                }}
              >
                <span className="icon"><i className="fas fa-paper-plane"></i></span>
                <span className="text">{ this.translate('Send email now!') }</span>
              </button>
              <div className='mt-2 alert alert-info'>
                { this.translate('Emails will be sent only to recipients who did not receive email yet.') }
              </div>
              <div className='mt-2 alert alert-info'>
                { this.translate('Unsubscribed and invalid recipients will be ignored.') }
              </div>
              {this.state.emailLaunchInfo && this.state.emailLaunchInfo.recipients ? <>
                <div className='card mt-2'>
                  <div className='card-header'>{ this.translate('Statistics') }</div>
                  <div className='card-body flex flex-col gap-1'>
                    <div className='badge'>
                      {this.translate('Recipients')}: {this.state.emailLaunchInfo.recipients.length}
                    </div>
                    <div className='badge'>
                      {this.translate('Emails sent')}: {emailsSent}
                    </div>
                    <div className='badge badge-warning'>
                      {this.translate('Invalid recipients')}: {invalidRecipientsCount} ({Math.round(invalidRecipientsCount / this.state.emailLaunchInfo.recipients.length * 100)} %)
                    </div>
                    <div className='badge badge-danger'>
                      {this.translate('Unsubscribed recipients')}: {unsubscribedRecipientsCount} ({Math.round(unsubscribedRecipientsCount / this.state.emailLaunchInfo.recipients.length * 100)} %)
                    </div>
                  </div>
                </div>
                <div className='card mt-2'>
                  <div className='card-header'>{ this.translate('Potential leads') }</div>
                  <div className='card-body flex flex-wrap gap-1'>
                    {potentialLeads.map((email, key) => {
                      return <div key={key} className='badge'>{email}</div>;
                    })}
                  </div>
                </div>
              </> : null}

              {this.state.launchResult && this.state.launchResult.status == 'success' ?
                <div className='alert alert-success mt-2'>{ this.translate('Email was sent.') }</div>
              : null}
              {this.state.launchResult && this.state.launchResult.status != 'success' ?
                <div className='alert alert-danger mt-2'>
                  { this.translate('Error occured when launching the email.') }<br/>
                  <b>{this.state.launchResult.message}</b>
                </div>
              : null}
            </div>
            <div className='card grow'>
              <div className='card-header'>{ this.translate('Recipients') }</div>
              <div className='card-body'>
                {this.state.emailLaunchInfo && this.state.emailLaunchInfo.recipients ? 
                  <table className='table-default dense'>
                    <thead>
                      <tr>
                        <th rowSpan={2}>#</th>
                        <th rowSpan={2}>{ this.translate('Email') }</th>
                        <th rowSpan={2}>{ this.translate('Status') }</th>
                        <th rowSpan={2}>{ this.translate('Clicks') }</th>
                        <th colSpan={2}>{ this.translate('Bot score') }</th>
                      </tr>
                      <tr>
                        <th>{ this.translate('Total') }</th>
                        <th>{ this.translate('Details') }</th>
                      </tr>
                    </thead>
                    <tbody>
                      {this.state.emailLaunchInfo.recipients.map((item, key) => {
                        let botScoreTotal = 0;
                        item.CLICKS.map((click, key) => {
                          botScoreTotal += click.bot_score;
                        });

                        let recentlyContacted = this.state.emailLaunchInfo.recentlyContacted[item.email];

                        return <tr>
                          <td className='text-nowrap'>{key+1}</td>
                          <td className={'text-nowrap' + (item.CLICKS.length > 0 ? ' bg-green-100' : '')}>{item.email}</td>
                          <td className='text-nowrap'>
                            {item.id_mail > 0 ? <>
                              {item.MAIL?.datetime_sent
                                ? <div className='badge badge-success'>{this.translate('Sent')} {item.MAIL?.datetime_sent}</div>
                                : <div className='badge badge-warning'>{this.translate('Scheduled')} {item.MAIL?.datetime_scheduled_to_send}</div>
                              }
                            </> : <div className='badge'>{this.translate('Not scheduled yet')}</div>}
                            {item.STATUS?.is_unsubscribed ? <div className='badge badge-danger'>{this.translate('Unsubscribed')}</div> : null}
                            {item.STATUS?.is_invalid ? <div className='badge badge-warning'>{this.translate('Invalid')}</div> : null}
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
                : <div className='alert alert-warning'>{ this.translate('Loading information about recipients and launch status.') }</div>}
              </div>
            </div>
          </div>
        </>;
      break;

      case 'clicks':
        return <TableEmailClicks
          parentForm={this}
          tag="table_email_click"
          uid={this.props.uid + "_table_email_click"}
          idEmail={R.id}
        />;
      break;

      case 'recipients':
        return <div className='flex gap-2'>
          <div className='flex-3'>
            <TableRecipients
              tag='table_email_recipients'
              ref={this.refTableRecipients}
              parentForm={this}
              uid={this.props.uid + "_table_email_recipient"}
              idEmail={R.id}
              view='briefOverview'
              onAfterLoadData={(table: any) => {
                this.setState({ recipients: table.state.data.records });
              }}
            />
          </div>
          <div className='flex-1 gap-2'>
            <div className='card'>
              <div className='card-header'>{this.translate('Import emails')}</div>
              <div className='card-body'>
                <textarea
                  className='w-full h-80'
                  placeholder={this.translate('One email per line.')}
                  ref={this.refEmails}
                ></textarea>
                <button
                  className='btn btn-add-outline mt-2 w-full'
                  onClick={() => {
                    request.post(
                      'email-marketing/api/import-emails',
                      {
                        idEmail: R.id,
                        emails: this.refEmails.current.value,
                      },
                      {},
                      (data: any) => {
                        this.refTableRecipients.current.reload();
                      }
                    )
                  }}
                >
                  <span className='icon'><i className='fas fa-upload'></i></span>
                  <span className='text'>{this.translate('Import emails')}</span>
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
                          idEmail: R.id                      },
                        {},
                        (data: any) => {
                          this.refTableRecipients.current.reload();
                        }
                      );
                    }
                  }}
                >
                  <span className='icon'><i className='fas fa-trash'></i></span>
                  <span className='text'>{this.translate('Remove all recipients')}</span>
                </button>
              </div>
            </div>
          </div>
        </div>;
      break;

      default:
        return super.renderTab(tabUid);
      break;
    }
  }
}

