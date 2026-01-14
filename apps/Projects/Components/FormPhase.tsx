import React, { Component } from 'react'
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface FormPhaseProps extends FormExtendedProps { }
interface FormPhaseState extends FormExtendedState { }

export default class FormPhase<P, S> extends FormExtended<FormPhaseProps, FormPhaseState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/Projects/Models/Team',
  }

  props: FormPhaseProps;
  state: FormPhaseState;

  translationContext: string = 'Hubleto\\App\\Community\\Projects\\Loader';
  translationContextInner: string = 'Components\\FormPhase';

  constructor(props: FormPhaseProps) {
    super(props);
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate('Phase')}</small>
      <h2>Record #{this.state.record.id ?? '0'}</h2>
    </>;
  }

}
