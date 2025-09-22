import React, { Component } from 'react'
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import TablePermissions from './TablePermissions';
import TableUsages from './TableUsages';

interface FormKeyProps extends HubletoFormProps { }
interface FormKeyState extends HubletoFormState { }

export default class FormKey<P, S> extends HubletoForm<FormKeyProps, FormKeyState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'Hubleto/App/Community/Api/Models/Key',
  }

  props: FormKeyProps;
  state: FormKeyState;

  translationContext: string = 'Hubleto\\App\\Community\\Api\\Loader';
  translationContextInner: string = 'Components\\FormKey';

  constructor(props: FormKeyProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormKeyProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: this.translate('Key') },
        { uid: 'usage', title: this.translate('Usage') },
      ]
    };
  }

  getRecordFormUrl(): string {
    return 'api/keys/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  renderTitle(): JSX.Element {
    return <>
      <small>API Key</small>
      <h2>{this.state.record.key ?? '-'}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <>
          {this.inputWrapper('key')}
          {this.inputWrapper('valid_until')}
          {this.inputWrapper('is_enabled')}
          {this.inputWrapper('notes')}
          {this.inputWrapper('ip_address_blacklist')}
          {this.inputWrapper('ip_address_whitelist')}
          {this.inputWrapper('created')}
          {this.inputWrapper('id_created_by')}
          {this.divider('Permissions')}
          <TablePermissions
            uid={this.props.uid + "_table_permissions"}
            tag={this.props.uid + "_table_permissions"}
            parentForm={this}
            idKey={R.id}
          />
        </>;
      break;
      case 'usage':
        return <>
          <TableUsages
            uid={this.props.uid + "_table_usage"}
            tag={this.props.uid + "_table_usage"}
            parentForm={this}
            idKey={R.id}
            readonly={true}
          />
        </>;
      break;
    }
  }

}
