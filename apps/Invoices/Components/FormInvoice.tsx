import React, { Component } from 'react'
import Form, { FormProps, FormState } from 'adios/Form';

interface FormInvoiceProps extends FormProps {
}

interface FormInvoiceState extends FormState {
}

export default class FormInvoice extends Form<FormInvoiceProps, FormInvoiceState> {
  static defaultProps = {
    ...Form.defaultProps,
    description: {
      ui: { headerClassName: 'bg-indigo-50', },
    }
  }

  props: FormInvoiceProps;
  state: FormInvoiceState;

  translationContext: string = 'mod.core.invoices.formInvoice';

  constructor(props: FormInvoiceProps) {
    super(props);
  }

  renderTitle(): JSX.Element {
    const r = this.state.record;
    return <>
      <h2>{r.number ? r.number : '---'}</h2>
      <div className="badge border-indigo-500 text-indigo-500 text-lg">{globalThis.main.translate('Invoice')}</div>
    </>;
  }

  renderContent(): JSX.Element {
    return <>
      <div className="grid grid-cols-2 gap-1">
        <div>
          {this.inputWrapper('id_issued_by')}
          {this.inputWrapper('id_profile')}
          {this.inputWrapper('id_customer')}
          {this.state.id == -1 ? null : <>
            {this.inputWrapper('number')}
            {this.inputWrapper('vs')}
            {this.inputWrapper('cs')}
            {this.inputWrapper('ss')}
          </>}
        </div>
        {this.state.id == -1 ? null : <>
          <div>
            {this.inputWrapper('date_issu')}
            {this.inputWrapper('date_delivery')}
            {this.inputWrapper('date_due')}
            {this.inputWrapper('date_payment')}
            {this.inputWrapper('notes')}

            {this.state.record.datum_uhrady == '0000-00-00' ? <>
              <div className="alert alert-danger mt-1">
                {globalThis.main.translate('Invoice is not paid.')}
              </div>
            </> : null}
          </div>
        </>}
      </div>
      {this.state.id == -1 ? null : <>
        <div className="mt-4">
          <a
            className='btn btn-large'
            href={globalThis.main.config['accountUrl'] + '/invoices/print?id=' + this.state.record._idHash_}
            target="_blank"
          >
            <span className='icon'><i className='fas fa-print'></i></span>
            <span className='text'>{globalThis.main.translate('Print invoice')}</span>
          </a>
        </div>
        <div className="card mt-4">
          <div className="card-header">
            {globalThis.main.translate('Items')}
          </div>
          <div className="card-body">
            {/* <TableInvoiceItems
              uid={this.props.uid + '_table_items'}
              idInvoice={this.state.record.id}
              parentForm={this}
            ></TableInvoiceItems> */}
          </div>
        </div>
      </>}
    </>;
  }
}
