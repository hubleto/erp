import React, { Component } from 'react';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';

export interface FormGroupProps extends HubletoFormProps {}
export interface FormGroupState extends HubletoFormState {}

export default class FormGroup<P, S> extends HubletoForm<FormGroupProps,FormGroupState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'Hubleto/App/Community/Products/Models/Group',
  };

  props: FormGroupProps;
  state: FormGroupState;

  parentApp: string = 'Hubleto/App/Community/Products';

  translationContext: string = 'Hubleto\\App\\Community\\Products\\Loader';
  translationContextInner: string = 'Components\\FormGroup';

  constructor(props: FormGroupProps) {
    super(props);
    this.state = {
      ...this.getStateFromProps(props)
    };
  }

  getRecordFormUrl(): string {
    return 'products/groups/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate('Group')}</small>
      <h2>{this.state.record.title ?? '-'}</h2>
    </>;
  }

}