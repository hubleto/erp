import React, { Component } from 'react'
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface FormLevelProps extends FormExtendedProps { }
interface FormLevelState extends FormExtendedState { }

export default class FormLevel<P, S> extends FormExtended<FormLevelProps, FormLevelState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/Leads/Models/Team',
  }

  props: FormLevelProps;
  state: FormLevelState;

  translationContext: string = 'Hubleto\\App\\Community\\Leads\\Loader';
  translationContextInner: string = 'Components\\FormLevel';

  constructor(props: FormLevelProps) {
    super(props);
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate("Level")}</small>
      <h2>{this.translate("Record")} #{this.state.record.id ?? '0'}</h2>
    </>;
  }

}
