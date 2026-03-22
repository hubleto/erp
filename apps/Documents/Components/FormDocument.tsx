import React, { Component } from 'react';
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import { getUrlParam } from '@hubleto/react-ui/core/Helper';

export interface FormDocumentProps extends FormExtendedProps {
}
export interface FormDocumentState extends FormExtendedState {}

export default class FormDocument<P, S> extends FormExtended<FormDocumentProps,FormDocumentState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/Documents/Models/Document',
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
    const downloadUrl = R.hyperlink ?? globalThis.hubleto.config.projectUrl + '/documents/download?f=' + (R.FOLDER?.uid ?? '') + '&d=' + R.uid;

    switch (tabUid) {
      case 'default':
        return <div className='flex gap-2 h-full'>
          <div className='flex-1'>
            {this.inputWrapper('id_folder')}
            {this.inputWrapper('name', {cssClass: 'text-2xl'})}
            {this.inputWrapper('file')}
            {this.inputWrapper('hyperlink')}
            {this.inputWrapper('is_public')}
            <div className='mt-16 text-center'>
              <a
                href={downloadUrl}
                target='_blank'
                className='btn btn-extra-large btn-primary-outline'
              >
                <span className='icon'><i className='fas fa-download'></i></span>
                <span className='text'>{this.translate('Download')}</span>
              </a>
            </div>
          </div>
          <div className='flex-2'>
            <div className='card h-full'>
              <div className='card-header'>{this.translate('Preview')}</div>
              <div className='card-body h-full'>
                <iframe className='w-full h-full' src={downloadUrl}></iframe>
              </div>
            </div>
          </div>
        </div>
      ;
    };
  }
}

