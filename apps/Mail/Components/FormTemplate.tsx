import React, { Component } from 'react';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';

export interface FormTemplateProps extends HubletoFormProps {}
export interface FormTemplateState extends HubletoFormState {}

export default class FormTemplate<P, S> extends HubletoForm<FormTemplateProps,FormTemplateState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'Hubleto/App/Community/Mail/Models/Template',
  };

  props: FormTemplateProps;
  state: FormTemplateState;

  translationContext: string = 'Hubleto\\App\\Community\\Mail\\Loader::Components\\FormTemplate';

  constructor(props: FormTemplateProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormTemplateProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  getRecordFormUrl(): string {
    return 'mail/templates/' + this.state.record.id;
  }

  renderTitle(): null|JSX.Element {
    return <>
      <h2>{this.state.record.subject ? this.state.record.subject : ''}</h2>
      <small>Template</small>
    </>;
  }

  sendMail() {
  }

  renderContent(): JSX.Element {

    return <>
      {this.inputWrapper('subject')}
      {this.inputWrapper('body_html')}
    </>;
  }
}

