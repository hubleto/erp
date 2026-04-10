import React, { Component, createRef, useRef, ChangeEvent } from 'react';
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import TablePosts from './TablePosts';

export interface FormPostProps extends FormExtendedProps {
}

export interface FormPostState extends FormExtendedState {
}

export default class FormPost<P, S> extends FormExtended<FormPostProps,FormPostState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    icon: 'fas fa-money-check-dollar',
    model: 'Hubleto/App/Community/Issues/Models/Post',
    renderWorkflowUi: true,
    renderOwnerManagerUi: true,
  };

  props: FormPostProps;
  state: FormPostState;

  translationContext: string = 'Hubleto\\App\\Community\\Issues\\Loader';
  translationContextInner: string = 'Components\\FormPost';

  parentApp: string = 'Hubleto/App/Community/Issues';

  constructor(props: FormPostProps) {
    super(props);

    this.state = {
      ...this.getStateFromProps(props),
    };
  }

  getTabsLeft() {
    return [
      { uid: 'default', title: <b>{this.translate('Post')}</b> },
      ...super.getTabsLeft(),
    ];
  }

  getStateFromProps(props: FormPostProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  getRecordFormUrl(): string {
    return 'issues/posts/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  contentClassName(): string
  {
    return this.state.record.is_closed ? 'bg-slate-100' : '';
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate('Post')}</small>
      <h2>{this.state.record.title ?? '-'}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <>
          {this.inputWrapper('id_issue')}
          {this.inputWrapper('from')}
          {this.inputWrapper('content')}
          {this.inputWrapper('id_mail')}
        </>;
      break;

      default:
        return super.renderTab(tabUid);
      break;
    }
  }

}