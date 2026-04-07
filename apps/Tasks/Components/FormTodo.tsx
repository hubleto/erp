import React, { Component } from 'react'
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';

interface FormTodoProps extends FormExtendedProps { }
interface FormTodoState extends FormExtendedState { }

export default class FormTodo<P, S> extends FormExtended<FormTodoProps, FormTodoState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    icon: 'fas fa-list-check',
    model: 'Hubleto/App/Community/Tasks/Models/Todo',
  }

  props: FormTodoProps;
  state: FormTodoState;

  translationContext: string = 'Hubleto\\App\\Community\\Tasks\\Loader';
  translationContextInner: string = 'Components\\FormTodo';

  refInputNewTodo: any;

  constructor(props: FormTodoProps) {
    super(props);
    this.refInputNewTodo = React.createRef();
  }

  getStateFromProps(props: FormTodoProps) {
    return {
      newTodo: '',
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Task')}</b> },
        ...this.getCustomTabs()
      ]
    }
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
      saveRelations: ['TODO'],
    }
  }

  getRecordFormUrl(): string {
    return 'tasks/todo/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate('Todo')}</small>
      <h2>{this.state.record.identifier ?? '-'}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <div>
          {this.inputWrapper('id_task')}
          {this.inputWrapper('id_responsible')}
          {this.inputWrapper('todo')}
          {this.inputWrapper('is_closed')}
          {this.inputWrapper('date_deadline')}
        </div>;
      break;

      default:
        return super.renderTab(tabUid);
      break;
    }
  }

}
