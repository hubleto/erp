import React, { Component } from 'react';
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';

export interface FormDocumentReviewProps extends FormExtendedProps {
}
export interface FormDocumentReviewState extends FormExtendedState {}

export default class FormDocumentReview<P, S> extends FormExtended<FormDocumentReviewProps,FormDocumentReviewState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/Documents/Models/DocumentReview',
  };

  props: FormDocumentReviewProps;
  state: FormDocumentReviewState;

  translationContext: string = 'Hubleto\\App\\Community\\Documents\\Loader';
  translationContextInner: string = 'Components\\FormDocumentReview';

  constructor(props: FormDocumentReviewProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormDocumentReviewProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Document')}</b> },
      ]
    };
  }

  getRecordFormUrl(): string {
    return 'documents/reviews/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate("Document review")}</small>
      <h2>{this.state.record.name ? this.state.record.name : '-'}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <div className='flex gap-2 h-full'>
          <div className='flex-1'>
            {this.inputWrapper('id_document')}
            {this.inputWrapper('requested_on')}
            {this.inputWrapper('id_requested_by')}
            {this.inputWrapper('reviewed_on')}
            {this.inputWrapper('id_reviewed_by')}
            {this.inputWrapper('comments')}
          </div>
        </div>
      ;
    };
  }
}

