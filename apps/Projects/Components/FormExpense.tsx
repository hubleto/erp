import React, { Component } from 'react'
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';

interface FormExpenseProps extends FormExtendedProps { }
interface FormExpenseState extends FormExtendedState { }

export default class FormExpense<P, S> extends FormExtended<FormExpenseProps, FormExpenseState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/Projects/Models/Expense',
  }

  props: FormExpenseProps;
  state: FormExpenseState;

  translationContext: string = 'Hubleto\\App\\Community\\Projects\\Loader';
  translationContextInner: string = 'Components\\FormExpense';

  constructor(props: FormExpenseProps) {
    super(props);
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate('Expense')}</small>
      <h2>{this.state.record.reason ?? '-'}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <div>
          {this.inputWrapper('id_project')}
          {this.inputWrapper('reason')}
          {this.inputWrapper('date')}
          {this.inputWrapper('amount')}
          {this.inputWrapper('id_approved_by')}
          {this.inputWrapper('id_spent_by')}
        </div>;
      break;
    }
  }

}
