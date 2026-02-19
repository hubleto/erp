import React, { Component } from 'react'
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface FormQuoteProps extends FormExtendedProps { }
interface FormQuoteState extends FormExtendedState { }

export default class FormQuote<P, S> extends FormExtended<FormQuoteProps, FormQuoteState> {
  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/Orders/Models/Quote'
  }

  props: FormQuoteProps;
  state: FormQuoteState;

  translationContext: string = 'Hubleto\\App\\Community\\Orders';
  translationContextInner: string = 'Components\\FormQuote';

  constructor(props: FormQuoteProps) {
    super(props);
  }

  getStateFromProps(props: FormQuoteProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Quote')}</b> },
      ]
    };
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
    }
  }

  getRecordFormUrl(): string {
    return 'quotes/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  renderTitle(): JSX.Element {
    const R = this.state.record;
    return <>
      <small>{this.translate('Quote')}</small>
      <h2>{R.id <= 0 ? this.translate('New') : R.id}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <div className='flex gap-2'>
          <div className='flex-1'>
            {this.inputWrapper('id_order')}
            {this.inputWrapper('version')}
            {this.inputWrapper('summary')}
            {this.inputWrapper('date_created')}
            {this.inputWrapper('date_sent')}
            {this.inputWrapper('id_approved_by')}
            {this.inputWrapper('date_approved')}
          </div>
          <div className='flex-2'>
            {[1,2,3,4,5].map((i, key) => {
              return <div key={key}>
                {this.divider('Document #' + i)}
                <div className='flex gap-2'>
                  <div className='flex-3'>
                    {this.inputWrapper('online_document_' + i, {wrapperCssClass: 'flex gap-2'})}
                    {this.inputWrapper('final_pdf_' + i, {wrapperCssClass: 'flex gap-2'})}
                  </div>
                  <div className='flex-2'>
                    {this.input('notes_document_' + i, {cssStyle: {height: '4.5em'}})}
                  </div>
                </div>
              </div>;
            })}
          </div>
        </div>;
      break;
    }

  }

}
