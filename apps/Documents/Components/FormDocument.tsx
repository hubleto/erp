import React, { Component } from 'react';
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import TableDocumentVersions from './TableDocumentVersions';
import TableDocumentReviews from './TableDocumentReviews';

export interface FormDocumentProps extends FormExtendedProps {
}
export interface FormDocumentState extends FormExtendedState {}

export default class FormDocument<P, S> extends FormExtended<FormDocumentProps,FormDocumentState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/Documents/Models/Document',
    renderWorkflowUi: true,
  };

  props: FormDocumentProps;
  state: FormDocumentState;

  translationContext: string = 'Hubleto\\App\\Community\\Documents\\Loader';
  translationContextInner: string = 'Components\\FormDocument';

  constructor(props: FormDocumentProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormDocumentProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Document')}</b> },
      ]
    };
  }

  getRecordFormUrl(): string {
    return 'documents/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate("Document")}</small>
      <h2>{this.state.record.name ? this.state.record.name : '-'}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <div className='flex gap-2'>
          <div className='flex-2'>
            {this.inputWrapper('uid', {readonly: true})}
            <div className='flex gap-2'>
              {this.inputWrapper('model')}
              {this.inputWrapper('record_id')}
            </div>
            <div className='flex gap-2'>
              {this.inputWrapper('id_created_by')}
              {this.inputWrapper('created_on')}
            </div>
            {this.inputWrapper('name', {cssClass: 'text-2xl'})}
            <div className='card'>
              <div className='card-header'>{this.translate('Versions')}</div>
              <div className='card-body'>
                <TableDocumentVersions
                  key={"table_documents_versions"}
                  tag={"table_documents_versions"}
                  parentForm={this}
                  readonly={true}
                  uid={this.props.uid + "_table_documents_versions"}
                  idDocument={R.id}
                />
              </div>
            </div>
          </div>
          <div className='flex-1'>
            <TableDocumentReviews
              key={"table_documents_reviews"}
              tag={"table_documents_reviews"}
              parentForm={this}
              uid={this.props.uid + "_table_documents_reviews"}
              idDocument={R.id}
            />
          </div>
        </div>
      break;
    };
  }
}

