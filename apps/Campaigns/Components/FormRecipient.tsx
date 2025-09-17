import React, { Component } from 'react';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import request from '@hubleto/react-ui/core/Request';

export interface FormRecipientProps extends HubletoFormProps {}
export interface FormRecipientState extends HubletoFormState {
  mailPreviewInfo?: any,
}

export default class FormRecipient<P, S> extends HubletoForm<FormRecipientProps, FormRecipientState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'Hubleto/App/Community/Campaigns/Models/Recipient',
  };

  props: FormRecipientProps;
  state: FormRecipientState;

  translationContext: string = 'Hubleto\\App\\Community\\Campaigns\\Loader::Components\\FormRecipient';

  parentApp: string = 'Hubleto/App/Community/Campaigns';

  constructor(props: FormRecipientProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormRecipientProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Campaign')}</b> },
      ]
    };
  }

  getRecordFormUrl(): string {
    return 'campaigns/recipients/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate("Recipient")}</small>
      <h2>{this.state.record.email ?? '-'}</h2>
    </>;
  }

  onAfterRecordLoaded(record: any) {
    request.post(
      'campaigns/api/get-mail-preview-info',
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
              {this.inputWrapper('id_campaign')}
              {this.inputWrapper('id_contact')}
              {this.inputWrapper('email')}
              {this.inputWrapper('first_name')}
              {this.inputWrapper('last_name')}
              {this.inputWrapper('salutation')}
              {this.inputWrapper('id_mail')}
            </div>
            <div className='flex-1'>
              <div className='flex-2'>
                <div className='card card-info'>
                  <div className='card-header'>Mail preview</div>
                  <div className='card-body'>
                    {mailPreviewInfo && mailPreviewInfo.bodyHtml != '' ? <>
                      <div className='mt-2'><b>Preview</b></div>
                      <div
                        className='text-blue-800 max-h-72'
                        dangerouslySetInnerHTML={{__html: mailPreviewInfo.bodyHtml}}
                      ></div>
                    </> : <div>
                      No mail preview available.
                    </div>}
                  </div>
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

