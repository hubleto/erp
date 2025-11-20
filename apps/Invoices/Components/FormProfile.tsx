import React, { Component } from 'react'
import HubletoForm, {HubletoFormProps, HubletoFormState} from "@hubleto/react-ui/ext/HubletoForm";
import request from '@hubleto/react-ui/core/Request';

interface FormProfileProps extends HubletoFormProps {
}

interface FormProfileState extends HubletoFormState {
}

export default class FormProfile extends HubletoForm<FormProfileProps, FormProfileState> {
  static defaultProps = {
    ...HubletoForm.defaultProps,
    icon: 'fas fa-file-invoice',
    description: {
      ui: { headerClassName: 'bg-indigo-50', },
    },
    renderWorkflowUi: true,
  }

  props: FormProfileProps;
  state: FormProfileState;

  translationContext: string = 'Hubleto\\App\\Community\\Invoices\\Loader';
  translationContextInner: string = 'Components\\FormProfile';

  constructor(props: FormProfileProps) {
    super(props);
  }

  getStateFromProps(props: FormProfileProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Invoice')}</b> },
        ...this.getCustomTabs()
      ],
    };
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
    }
  }

  getRecordFormUrl(): string {
    return 'invoices/profiles/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  renderTitle(): JSX.Element {
    const R = this.state.record;
    return <>
      <small>{this.translate('Invoice profile')}</small>
      <h2>{R.name}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <>
          <div className="flex gap-2 mt-2">
            <div className='flex-1'>
              {this.inputWrapper('name')}
              {this.inputWrapper('id_company')}
              {this.inputWrapper('id_template')}
              {this.inputWrapper('numbering_pattern')}
            </div>
          </div>
        </>;
      break;
    }
  }
}
