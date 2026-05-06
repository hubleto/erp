import React, { Component } from 'react';
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import TableReviews from './TableReviews';

export interface FormDocumentVersionProps extends FormExtendedProps {
}
export interface FormDocumentVersionState extends FormExtendedState {}

export default class FormDocumentVersion<P, S> extends FormExtended<FormDocumentVersionProps,FormDocumentVersionState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/Documents/Models/DocumentVersion',
  };

  props: FormDocumentVersionProps;
  state: FormDocumentVersionState;

  translationContext: string = 'Hubleto\\App\\Community\\Documents\\Loader';
  translationContextInner: string = 'Components\\FormDocumentVersion';

  constructor(props: FormDocumentVersionProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormDocumentVersionProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Document')}</b> },
      ]
    };
  }

  getRecordFormUrl(): string {
    return 'documents/versions/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate("Document version")}</small>
      <h2>{this.state.record.name ? this.state.record.name : '-'}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <div className='flex gap-2 h-full'>
          <div className='flex-3'>
            {this.inputWrapper('uid', {readonly: true})}
            {this.inputWrapper('id_document')}
            {this.inputWrapper('version')}
            {this.inputWrapper('file')}
            {this.inputWrapper('created_on')}
            {this.inputWrapper('id_created_by')}
          </div>
          <div className='flex-1'>
            <TableReviews
              key={"table_documents_versions_reviews"}
              tag={"table_documents_versions_reviews"}
              parentForm={this}
              uid={this.props.uid + "_table_documents_versions_reviews"}
              idDocument={R.id}
              idVersion={R.id}
            />
          </div>
        </div>
      ;
    };
  }
}

