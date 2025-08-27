import React, { Component } from 'react';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';

export interface FormTemplateProps extends HubletoFormProps {}
export interface FormTemplateState extends HubletoFormState {}

export default class FormTemplate<P, S> extends HubletoForm<FormTemplateProps,FormTemplateState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Documents/Models/Document',
  };

  props: FormTemplateProps;
  state: FormTemplateState;

  translationContext: string = 'HubletoApp\\Community\\Documents\\Loader::Components\\FormTemplate';

  constructor(props: FormTemplateProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormTemplateProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

}

