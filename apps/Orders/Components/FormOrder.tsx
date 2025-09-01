import React, { Component, createRef, useRef } from 'react';
import { getUrlParam } from '@hubleto/react-ui/core/Helper';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import TableOrderProducts from '@hubleto/apps/Orders/Components/TableOrderProducts';
import TableDocuments from '@hubleto/apps/Documents/Components/TableDocuments';
import request from "@hubleto/react-ui/core/Request";
import TableHistories from './TableHistories';
import PipelineSelector from '../../Pipeline/Components/PipelineSelector';
import FormInput from '@hubleto/react-ui/core/FormInput';

export interface FormOrderProps extends HubletoFormProps {
}

export interface FormOrderState extends HubletoFormState {
  newEntryId: number,
}

export default class FormOrder<P, S> extends HubletoForm<FormOrderProps,FormOrderState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'Hubleto/App/Community/Orders/Models/Order',
  };

  props: FormOrderProps;
  state: FormOrderState;

  translationContext: string = 'Hubleto\\App\\Community\\Orders\\Loader::Components\\FormOrder';

  parentApp: string = 'Hubleto/App/Community/Orders';

  constructor(props: FormOrderProps) {
    super(props);
    this.state = {
      ...this.getStateFromProps(props),
      newEntryId: -1,
    };
  }

  getStateFromProps(props: FormOrderProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Order')}</b> },
        { uid: 'products', title: this.translate('Products'), showCountFor: 'PRODUCTS' },
        { uid: 'documents', title: this.translate('Documents'), showCountFor: 'DOCUMENTS' },
        // { uid: 'invoices', title: this.translate('Invoices'), showCountFor: 'INVOICES' },
        { uid: 'history', icon: 'fas fa-clock-rotate-left', position: 'right' },
        ...(this.getParentApp()?.getFormTabs() ?? [])
      ]
    };
  }

  getRecordFormUrl(): string {
    return 'orders/' + this.state.record.id;
  }

  contentClassName(): string
  {
    return this.state.record.is_closed ? 'opacity-85 bg-slate-100' : '';
  }

  renderTitle(): JSX.Element {
    return <>
      <small>Order</small>
      <h2>{this.state.record.identifier ?? '-'}</h2>
    </>;
  }

  getSumPrice(recordProducts: any) {
    var sumPrice = 0;
    recordProducts.map((product, index) => {
      if (product.unit_price && product.amount && product._toBeDeleted_ != true) {
        var sum = product.unit_price * product.amount;
        if (product.vat) sum = sum + (sum * (product.vat / 100));
        if (product.discount) sum = sum - (sum * (product.discount / 100));
        sumPrice += sum;
      }
    });
    return Number(sumPrice.toFixed(2));
  }

  getFormHeaderButtons()
  {
    return [
      ...super.getFormHeaderButtons(),
      {
        title: 'Generate PDF',
        onClick: () => {
          request.post(
            'orders/api/generate-pdf',
            {idOrder: this.state.record.id},
            {},
            (result: any) => {
              console.log(result);
              if (result.idDocument) {
                window.open(globalThis.main.config.projectUrl + '/documents/' + result.idDocument);
              }
            }
          );
        }
      },
      {
        title: 'Close order',
        onClick: () => { }
      }
    ]
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
        {this.inputWrapper('is_closed')}
      </>}
    </>
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':

        return <>
          <div className='card'>
            <div className='card-body flex flex-row gap-2'>
              <div className='grow'>
                <FormInput title={"Deal"}>
                  {R.DEALS ? R.DEALS.map((item, key) => {
                    return (item.DEAL ? <a
                      key={key}
                      className='badge'
                      href={globalThis.main.config.projectUrl + '/deals/' + item.DEAL.id}
                      target='_blank'
                    >{item.DEAL.identifier}</a> : '#');
                  }) : null}
                </FormInput>
                {this.inputWrapper('identifier')}
                {this.inputWrapper('title')}
                {<div className='flex flex-row *:w-1/2'>
                    {this.inputWrapper('price_excl_vat')}
                    {this.inputWrapper('price_incl_vat')}
                    {this.inputWrapper('id_currency')}
                </div>}
                {this.inputWrapper('date_order')}
                {this.inputWrapper('required_delivery_date')}
                {this.inputWrapper('shipping_info')}
              </div>
              <div className='border-l border-gray-200'></div>
              <div className='grow'>
                {this.inputWrapper('id_customer')}
                {this.inputWrapper('id_owner')}
                {this.inputWrapper('id_manager')}
                {this.inputWrapper('note')}
                {this.inputWrapper('id_template')}
              </div>
            </div>
          </div>
        </>;
      break;

      case 'products':
        return <TableOrderProducts
          tag={"table_order_product"}
          parentForm={this}
          uid={this.props.uid + "_table_order_product"}
          idOrder={R.id}
          // junctionTitle='Order'
          // junctionModel='Hubleto/App/Community/Orders/Models/OrderProduct'
          // junctionSourceColumn='id_order'
          // junctionSourceRecordId={R.id}
          // junctionDestinationColumn='id_product'
          readonly={R.is_archived == true ? false : !this.state.isInlineEditing}
        />;

      break;

      case 'documents':
        return <TableDocuments
          key={"table_order_document"}
          parentForm={this}
          uid={this.props.uid + "_table_order_document"}
          junctionTitle='Order'
          junctionModel='Hubleto/App/Community/Orders/Models/OrderDocument'
          junctionSourceColumn='id_order'
          junctionSourceRecordId={R.id}
          junctionDestinationColumn='id_document'
          readonly={R.is_archived == true ? false : !this.state.isInlineEditing}
        />;

      break;

      case 'history':
        return <>
          <TableHistories
            uid={this.props.uid + "_table_order_history"}
            data={{ data: R.HISTORY }}
            descriptionSource='props'
            description={{
              ui: {
                showHeader: false,
                showFooter: false,
              },
              permissions: {
                canCreate: false,
                canUpdate: false,
                canDelete: false,
                canRead: true,
              },
              columns: {
                short_description: { type: "text", title: "Short Description" },
                date: { type: "datetime", title: "Date Time"},
              },
              inputs: {
                short_description: { type: "text", title: "Short Description" },
                date: { type: "datetime", title: "Date Time"},
              }
            }}
            isUsedAsInput={true}
            isInlineEditing={false}
            onRowClick={(table: TableHistories, row: any) => table.openForm(row.id)}
          />
        </>;
      break;

      default:
        super.renderTab(tabUid);
      break;
    }
  }
}