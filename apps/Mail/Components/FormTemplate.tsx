import React, { Component } from 'react';
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';

export interface FormTemplateProps extends FormExtendedProps {}
export interface FormTemplateState extends FormExtendedState {}

export default class FormTemplate<P, S> extends FormExtended<FormTemplateProps,FormTemplateState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/Mail/Models/Template',
  };

  props: FormTemplateProps;
  state: FormTemplateState;

  translationContext: string = 'Hubleto\\App\\Community\\Mail\\Loader';
  translationContextInner: string = 'Components\\FormTemplate';

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
    return 'mail/templates/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  renderTitle(): null|JSX.Element {
    return <>
      <h2>{this.state.record.subject ? this.state.record.subject : ''}</h2>
      <small>{this.translate('Template')}</small>
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

