import React, { Component } from 'react';
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';

export interface FormPanelProps extends FormExtendedProps {}
export interface FormPanelState extends FormExtendedState {}

export default class FormPanel<P, S> extends FormExtended<FormPanelProps,FormPanelState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/Dashboards/Models/Panel',
  };

  props: FormPanelProps;
  state: FormPanelState;

  translationContext: string = 'Hubleto\\App\\Community\\Dasboards\\Loader';
  translationContextInner: string = 'Components\\FormPanel';

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
      <small>{this.translate('Dashboard panel')}</small>
      <h2>{this.state.record.title ? this.state.record.title : '-'}</h2>
    </>;
  }

  renderContent(): JSX.Element {
    const R = this.state.record;

    return <>
      {this.inputWrapper('id_dashboard')}
      {this.inputWrapper('board_url_slug', {
        cssClass: 'text-2xl',
        onChange: (input: any, value: any) => {
          const enumValues = input.props.enumValues;
          this.updateRecord({title: enumValues[value] ?? '-'})
        }
      })}
      {this.inputWrapper('title')}
      {this.inputWrapper('width')}
      {this.inputWrapper('configuration')}
    </>;
  }
}

