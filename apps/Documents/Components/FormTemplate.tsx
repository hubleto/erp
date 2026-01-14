import React, { Component } from 'react';
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';

export interface FormTemplateProps extends FormExtendedProps {}
export interface FormTemplateState extends FormExtendedState {}

export default class FormTemplate<P, S> extends FormExtended<FormTemplateProps,FormTemplateState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/Documents/Models/Document',
  };

  props: FormTemplateProps;
  state: FormTemplateState;

  translationContext: string = 'Hubleto\\App\\Community\\Documents\\Loader';
  translationContextInner: string = 'Components\\FormTemplate';

  constructor(props: FormTemplateProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormTemplateProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  getRecordFormUrl(): string {
    return 'documents/templates/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate('Document template')}</small>
      <h2>{this.state.record.name ?? '-'}</h2>
    </>;
  }
  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <>
          <div className="grid grid-cols-2 gap-1">
            <div>
              {this.inputWrapper('name')}
              {this.inputWrapper('used_for')}
            </div>
            <div>
              {this.inputWrapper('notes')}
            </div>
          </div>
          <div style={{height: 'calc(100vh - 260px)', overflowY: 'scroll'}}>
            {this.inputWrapper('content', {wrapperCssClass: 'overflow-y-auto'})}
          </div>
        </>;
      break;
    }
  }

}

