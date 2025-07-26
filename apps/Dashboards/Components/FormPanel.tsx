import React, { Component } from 'react';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';

export interface FormPanelProps extends HubletoFormProps {}
export interface FormPanelState extends HubletoFormState {}

export default class FormPanel<P, S> extends HubletoForm<FormPanelProps,FormPanelState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Dashboards/Models/Panel',
  };

  props: FormPanelProps;
  state: FormPanelState;

  translationContext: string = 'HubletoApp\\Community\\Documents\\Loader::Components\\FormPanel';

  constructor(props: FormPanelProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormPanelProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  renderTitle(): JSX.Element {
    return <>
      <h2>{this.state.record.title ? this.state.record.title : '-'}</h2>
      <small>Dashboard panel</small>
    </>;
  }

  renderContent(): JSX.Element {
    const R = this.state.record;

    return <>
      {this.inputWrapper('id_dashboard')}
      {this.inputWrapper('board_url_slug', {
        cssClass: 'text-2xl text-primary',
        onChange: (input: any, value: any) => {
          const enumValues = input.props.enumValues;
          this.updateRecord({title: enumValues[value] ?? '-'})
        }
      })}
      {this.inputWrapper('title')}
      {this.inputWrapper('configuration')}
    </>;
  }
}

