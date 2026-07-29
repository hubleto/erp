import React, { Component } from 'react'
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/components/cc/FormExtended';
import Table, { TableProps, TableState } from '@hubleto/react-ui/components/cc/Table';

interface FormTypeProps extends FormExtendedProps { }
interface FormTypeState extends FormExtendedState { }

export default class FormType<P, S> extends FormExtended<FormTypeProps, FormTypeState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/Events/Models/Team',
  }

  props: FormTypeProps;
  state: FormTypeState;

  translationContext: string = 'Hubleto\\App\\Community\\Events\\Loader';
  translationContextInner: string = 'Components\\FormType';

  constructor(props: FormTypeProps) {
    super(props);
  }

  renderTitle(): React.JSX.Element {
    return <>
      <small>{this.translate('Type')}</small>
      <h2>Record #{this.state.record.id ?? '0'}</h2>
    </>;
  }

}
