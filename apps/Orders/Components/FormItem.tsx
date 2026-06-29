import React, { Component } from 'react'
import FormExtended, {FormExtendedProps, FormExtendedState} from "@hubleto/react-ui/ext/FormExtended";

export interface FormItemProps extends FormExtendedProps {
  idOrder: number,
}

interface FormItemState extends FormExtendedState {
}

export default class FormItem extends FormExtended<FormItemProps, FormItemState> {
  static defaultProps = {
    ...FormExtended.defaultProps,
    icon: 'fas fa-file-invoice',
    description: {
      ui: { headerClassName: 'bg-indigo-50', },
    },
  }

  props: FormItemProps;
  state: FormItemState;

  translationContext: string = 'Hubleto\\App\\Community\\Orders\\Loader';
  translationContextInner: string = 'Components\\FormItem';

  constructor(props: FormItemProps) {
    super(props);
  }

  getStateFromProps(props: FormItemProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Order item')}</b> },
        ...this.getCustomTabs()
      ],
    };
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
      idOrder: this.props.idOrder,
    }
  }

  getRecordFormUrl(): string {
    return 'orders/items/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  renderTitle(): JSX.Element {
    const R = this.state.record;
    return <>
      <small>{this.translate('Item')}</small>
      <h2>{R.date}</h2>
    </>;
  }

  prepareRecordCopy() {
    return {
      ...this.state.record,
      title: 'Copy of ' + (this.state.record.title ?? ''),
      id_invoice_item: null,
      date_due: null,
      attachment_1: null,
      attachment_2: null,
      notes: null,
      id: -1
    };
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <>
          <div className="flex gap-2 mt-2">
            <div className='flex-5'>
              {this.inputWrapper('title')}
            </div>
            <div className='flex-1'>
              {this.renderOwnerManagerUi()}
            </div>
          </div>
          <div className="flex gap-2 mt-2">
            <div className='flex-1'>
              {this.inputWrapper('id_order')}
              {this.inputWrapper('id_product')}
              {this.inputWrapper('unit_price')}
              {this.inputWrapper('amount')}
              {this.inputWrapper('discount')}
              {this.inputWrapper('vat')}
              <div className='bg-slate-50 p-2'>
                <b>{this.translate('Summary')}</b><br/>
                <table className='table-default dense w-full'>
                  <thead>
                    <tr>
                      <th></th>
                      <th>{this.translate('Without discount')}</th>
                      <th>{this.translate('With discount')}</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><b>{R.title}</b></td>
                      <td className='text-nowrap'>
                        <div className='flex gap-2'>
                          <div>{globalThis.hubleto.currencyFormat(R.unit_price, 4)}</div>
                          <div>x</div>
                          <div>{globalThis.hubleto.numberFormat(R.amount, 4)}</div>
                        </div>
                      </td>
                      <td className='text-nowrap'>
                        <div className='flex gap-2'>
                          <div>{globalThis.hubleto.currencyFormat(R.unit_price * (1 - R.discount / 100), 4)}</div>
                          <div>x</div>
                          <div>{globalThis.hubleto.numberFormat(R.amount, 4)}</div>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>{this.translate('Excluding VAT')}</td>
                      <td className='text-nowrap'>{globalThis.hubleto.currencyFormat(R.unit_price * R.amount, 4)}</td>
                      <td className='text-nowrap'>{globalThis.hubleto.currencyFormat(R.unit_price * R.amount * (1 - R.discount / 100), 4)}</td>
                    </tr>
                    <tr>
                      <td>{this.translate('Including {vat}% VAT').replace('{vat}', globalThis.hubleto.numberFormat(R.vat, 0))}</td>
                      <td className='text-nowrap'>{globalThis.hubleto.currencyFormat(R.unit_price * R.amount * (1 + R.vat / 100), 4)}</td>
                      <td className='text-nowrap'>{globalThis.hubleto.currencyFormat(R.unit_price * R.amount * (1 + R.vat / 100) * (1 - R.discount / 100), 4)}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div className='flex-1'>
              <div className='card'>
                <div className='card-header'>
                  Dates
                </div>
                <div className='card-body'>
                  {this.inputWrapper('date_due')}
                  {this.inputWrapper('date_delivery')}
                  {this.divider('')}
                  {this.inputWrapper('charged_period_start')}
                  {this.inputWrapper('charged_period_end')}
                </div>
              </div>
              {this.inputWrapper('notes')}
              {this.inputWrapper('attachment_1')}
              {this.inputWrapper('attachment_2')}
              {this.inputWrapper('position')}
              {this.inputWrapper('is_chargeable')}
              {this.inputWrapper('id_invoice_item')}
            </div>
          </div>
        </>;
      break;
    }
  }
}
