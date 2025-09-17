import React, { Component } from 'react';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';

export interface FormRecipientProps extends HubletoFormProps {}
export interface FormRecipientState extends HubletoFormState {}

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
      <h2>{this.state.record.name ?? '-'}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <>
          <div className='w-full flex gap-2'>
            <div className='flex-1 border-r border-gray-100'>
              {this.inputWrapper('id_campaign')}
              {this.inputWrapper('id_contact')}
              {this.inputWrapper('email')}
              {this.inputWrapper('id_mail')}
            </div>
            <div className='flex-1'>
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

