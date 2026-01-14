import React, { Component } from 'react'
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface FormActivityTypeProps extends FormExtendedProps { }
interface FormActivityTypeState extends FormExtendedState { }

export default class FormActivityType<P, S> extends FormExtended<FormActivityTypeProps, FormActivityTypeState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/Worksheets/Models/Team',
  }

  props: FormActivityTypeProps;
  state: FormActivityTypeState;

  translationContext: string = 'Hubleto\\App\\Community\\Worksheets\\Loader';
  translationContextInner: string = 'Components\\FormActivityType';

  constructor(props: FormActivityTypeProps) {
    super(props);
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate('ActivityType')}</small>
      <h2>Record #{this.state.record.id ?? '0'}</h2>
    </>;
  }

}
