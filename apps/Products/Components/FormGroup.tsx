import React, { Component } from 'react';
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';

export interface FormGroupProps extends FormExtendedProps {}
export interface FormGroupState extends FormExtendedState {}

export default class FormGroup<P, S> extends FormExtended<FormGroupProps,FormGroupState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
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