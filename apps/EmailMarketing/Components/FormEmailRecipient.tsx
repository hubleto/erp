import React, { Component } from 'react';
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import request from '@hubleto/react-ui/core/Request';

export interface FormEmailRecipientProps extends FormExtendedProps {}
export interface FormEmailRecipientState extends FormExtendedState {
  mailPreviewInfo?: any,
}

export default class FormEmailRecipient<P, S> extends FormExtended<FormEmailRecipientProps, FormEmailRecipientState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/EmailMarketing/Models/Recipient',
  };

  props: FormEmailRecipientProps;
  state: FormEmailRecipientState;

  translationContext: string = 'Hubleto\\App\\Community\\EmailMarketing\\Loader';
  translationContextInner: string = 'Components\\FormEmailRecipient';

  parentApp: string = 'Hubleto/App/Community/EmailMarketing';

  constructor(props: FormEmailRecipientProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormEmailRecipientProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Recipient')}</b> },
      ]
    };
  }

  getRecordFormUrl(): string {
    return 'email-marketing/emails/recipients/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate("Recipient")}</small>
      <h2>{this.state.record.email ?? '-'}</h2>
    </>;
  }

  onAfterRecordLoaded(record: any) {
    request.post(
      'email-marketing/api/get-email-preview-info',
      {
        idRecipient: record.id,
      },
      {},
      (result: any) => {
        console.log(result);
        this.setState({mailPreviewInfo: result});
      }
    );

    return record;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;
    const mailPreviewInfo: any = this.state.mailPreviewInfo;

    switch (tabUid) {
      case 'default':
        return <>
          <div className='w-full flex gap-2'>
            <div className='flex-1 border-r border-gray-100'>
              {this.inputWrapper('id_email')}
              {this.inputWrapper('id_contact')}
              {this.inputWrapper('email')}
              {this.inputWrapper('phone_number')}
              {this.inputWrapper('first_name')}
              {this.inputWrapper('last_name')}
              {this.inputWrapper('salutation')}
              {this.inputWrapper('variables')}
              {this.inputWrapper('notes')}
            </div>
            <div className='flex-1'>
              <div className='card'>
                <div className='card-header'>{this.translate('Mail preview')}</div>
                <div className='card-body'>
                  {mailPreviewInfo && mailPreviewInfo.bodyHtml != '' ? <>
                    <div
                      dangerouslySetInnerHTML={{__html: mailPreviewInfo.bodyHtml}}
                    ></div>
                  </> : <div>
                    {this.translate('No mail preview available.')}
                  </div>}
                </div>
              </div>
            </div>
          </div>
        </>;
      break

      default:
        return super.renderTab(tabUid);
      break;
    }
  }
}

