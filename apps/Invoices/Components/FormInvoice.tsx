import React, { Component } from 'react'
import HubletoForm, {HubletoFormProps, HubletoFormState} from "@hubleto/react-ui/ext/HubletoForm";
import TableInvoiceItems from './TableInvoiceItems';
import TableDocuments from '@hubleto/apps/Documents/Components/TableDocuments';
import PipelineSelector from '../../Pipeline/Components/PipelineSelector';
import request from '@hubleto/react-ui/core/Request';

interface FormInvoiceProps extends HubletoFormProps {
}

interface FormInvoiceState extends HubletoFormState {
}

export default class FormInvoice extends HubletoForm<FormInvoiceProps, FormInvoiceState> {
  static defaultProps = {
    ...HubletoForm.defaultProps,
    description: {
      ui: { headerClassName: 'bg-indigo-50', },
    }
  }

  props: FormInvoiceProps;
  state: FormInvoiceState;

  translationContext: string = 'Hubleto\\App\\Community\\Invoices\\Loader::Components\\FormInvoice';

  constructor(props: FormInvoiceProps) {
    super(props);
  }

  getStateFromProps(props: FormInvoiceProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Invoice')}</b> },
        { uid: 'items', title: this.translate('Items'), showCountFor: 'ITEMS' },
        { uid: 'documents', title: this.translate('Documents'), showCountFor: 'DOCUMENTS' },
        ...(this.getParentApp()?.getFormTabs() ?? [])
      ],
    };
  }

  getFormHeaderButtons()
  {
    return [
      ...super.getFormHeaderButtons(),
      {
        title: 'Print to PDF',
        onClick: () => {
          request.post(
            'invoices/api/generate-pdf',
            {idInvoice: this.state.record.id},
            {},
            (result: any) => {
              if (result.idDocument) {
                window.open(globalThis.main.config.projectUrl + '/documents/' + result.idDocument);
              }
            }
          );
        }
      }
    ]
  }

  getRecordFormUrl(): string {
    return 'invoices/' + this.state.record.id;
  }

  renderTopMenu(): JSX.Element {
    const R = this.state.record;
    return <>
      {super.renderTopMenu()}
      {this.state.id <= 0 ? null : <>
        <PipelineSelector
          idPipeline={R.id_pipeline}
          idPipelineStep={R.id_pipeline_step}
          onPipelineChange={(idPipeline: number, idPipelineStep: number) => {
            this.updateRecord({id_pipeline: idPipeline, id_pipeline_step: idPipelineStep});
          }}
          onPipelineStepChange={(idPipelineStep: number, step: any) => {
            this.updateRecord({id_pipeline_step: idPipelineStep});
          }}
        ></PipelineSelector>
      </>}
    </>
  }

  renderTitle(): JSX.Element {
    const r = this.state.record;
    return <>
      <h2>{r.number ? r.number : '---'}</h2>
      <div className="badge border-indigo-500 text-indigo-500 text-lg">{this.translate('Invoice')}</div>
    </>;
  }

  renderTab(tab: string) {
    const R = this.state.record;

    switch (tab) {
      case 'default':
        return <>
          <div className="grid grid-cols-2 gap-1">
            <div>
              {this.inputWrapper('id_issued_by')}
              {this.inputWrapper('id_profile')}
              {this.inputWrapper('id_customer')}
              {this.inputWrapper('id_template')}
              {this.inputWrapper('id_currency')}
              {this.state.id == -1 ? null : <>
                {this.inputWrapper('number')}
                {this.inputWrapper('vs')}
                {this.inputWrapper('cs')}
                {this.inputWrapper('ss')}
                {this.inputWrapper('total_excl_vat')}
                {this.inputWrapper('total_incl_vat')}
              </>}
            </div>
            {this.state.id == -1 ? null : <>
              <div>
                {this.inputWrapper('date_issue')}
                {this.inputWrapper('date_delivery')}
                {this.inputWrapper('date_due')}
                {this.inputWrapper('date_payment')}
                {this.inputWrapper('notes')}
              </div>
            </>}
          </div>
        </>;
      break;

      case 'items':
        return <>
          {this.state.id == -1 ? null : <>
            {/* <div className="mt-4">
              <a
                className='btn btn-large'
                href={globalThis.main.config['accountUrl'] + '/invoices/print?id=' + this.state.record._idHash_}
                target="_blank"
              >
                <span className='icon'><i className='fas fa-print'></i></span>
                <span className='text'>{this.translate('Print invoice')}</span>
              </a>
            </div>
            <div className="card mt-4">
              <div className="card-header">
                {this.translate('Items')}
              </div>
              <div className="card-body"> */}
                <TableInvoiceItems
                  uid={this.props.uid + '_table_items'}
                  idInvoice={this.state.record.id}
                  parentForm={this}
                ></TableInvoiceItems>
              {/* </div>
            </div> */}
          </>}
        </>;
      break;

      case 'documents':
        return <>
          <TableDocuments
            uid={this.props.uid + "_table_invoice_documents"}
            tag={'table_invoice_documents'}
            parentForm={this}
            junctionModel='Hubleto\App\Community\Invoices\Models\InvoiceDocument'
            junctionSourceColumn='id_invoice'
            junctionDestinationColumn='id_document'
            junctionSourceRecordId={R.id}
          />
        </>
      break;

    }
  }
}
