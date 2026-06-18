import React, { Component } from 'react';
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import TableProductSuppliers from './TableProductSuppliers';
import Barcode from 'react-barcode';
import Int from '@hubleto/react-ui/core/Inputs/Int';
import Lookup from '@hubleto/react-ui/core/Inputs/Lookup';
import request from '@hubleto/react-ui/core/Request';

export interface FormProductProps extends FormExtendedProps {}
export interface FormProductState extends FormExtendedState {
  unitsById?: Record<string, string>,
}

export default class FormProduct<P, S> extends FormExtended<FormProductProps,FormProductState> {
  static UNIT_CATEGORY_BASE = 1;
  static UNIT_CATEGORY_CONTAINER = 2;

  static defaultProps: any = {
    ...FormExtended.defaultProps,
    model: 'Hubleto/App/Community/Products/Models/Product',
  };

  props: FormProductProps;
  state: FormProductState;

  parentApp: string = 'Hubleto/App/Community/Products';

  translationContext: string = 'Hubleto\\App\\Community\\Products\\Loader';
  translationContextInner: string = 'Components\\FormProduct';

  constructor(props: FormProductProps) {
    super(props);
    this.state = {
      ...this.getStateFromProps(props)
    };
  }

  getStateFromProps(props: FormProductProps) {
    return {
      ...super.getStateFromProps(props),
      unitsById: this.state?.unitsById ?? {},
      tabs: [
        { uid: 'default', title: <b>{this.translate('Product')}</b> },
        { uid: 'packaging', title: this.translate('Packaging') },
        { uid: 'gallery', title: this.translate('Gallery') },
        { uid: 'suppliers', title: this.translate('Suppliers') },
        ...this.getCustomTabs()
      ]
    };
  }

  componentDidMount() {
    super.componentDidMount();
    request.post(
      globalThis.hubleto.config.defaultLookupEndpoint ?? 'api/record/lookup',
      { model: 'Hubleto/App/Community/Products/Models/Unit', search: '', __IS_AJAX__: '1' },
      {},
      (data: any) => {
        const map: any = {};
        Object.keys(data ?? {}).forEach((id) => { map[id] = data[id]?._LOOKUP ?? ''; });
        this.setState({ unitsById: map });
      }
    );
  }

  unitName(id: any): string {
    return (id && this.state.unitsById) ? (this.state.unitsById[id] ?? '') : '';
  }

  getRecordFormUrl(): string {
    return 'products/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  getEndpointParams(): object {
    return {
      ...super.getEndpointParams() as any,
      saveRelations: ['PACKAGING'],
    };
  }

  updatePackaging(index: number, item: any, changedValues: any) {
    let newRecord = this.state.record;
    if (!newRecord.PACKAGING) newRecord.PACKAGING = [];
    newRecord.PACKAGING[index] = {...item, ...changedValues};
    newRecord.PACKAGING[index].id_product = { _useMasterRecordId_: true };
    this.updateRecord(newRecord);
  }

  movePackaging(index: number, dir: number) {
    let newRecord = this.state.record;
    const list = newRecord.PACKAGING ?? [];
    const target = index + dir;
    if (target < 0 || target >= list.length) return;
    [list[index], list[target]] = [list[target], list[index]];
    list.forEach((row: any, i: number) => { row.sort = i; });
    this.updateRecord(newRecord);
  }

  renderTitle(): React.JSX.Element {
    return <>
      <small>{this.translate('Product')}</small>
      <h2>{this.state.record.ean ?? '-'} {this.state.record.name ?? '-'}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <>
          <div className='grid grid-cols-2 gap-2'>
            <div className='border-r border-gray-200'>
              <div className='flex gap-2'>
                <div className='flex grow'>{this.inputWrapper('ean')}</div>
                <div className='flex grow'><Barcode value={R.ean} height={30} /></div>
              </div>
              {this.inputWrapper('name', {cssClass: 'text-2xl'})}
              {this.inputWrapper('is_on_sale')}
              {this.inputWrapper('sales_price')}
              {this.inputWrapper('id_group')}
              {this.inputWrapper('id_category')}
              {this.inputWrapper('vat')}
              {this.inputWrapper('margin')}
              {this.inputWrapper('id_unit', { customEndpointParams: { unitCategory: FormProduct.UNIT_CATEGORY_BASE } })}
              {this.inputWrapper('description')}
              {this.inputWrapper('is_single_order_possible')}
              {this.inputWrapper('show_price')}
              {this.inputWrapper('needs_reordering')}
            </div>
            <div className=''>
              {this.inputWrapper('type')}
              {this.inputWrapper('invoicing_policy')}
              {this.inputWrapper('sale_ended')}
              {this.inputWrapper('price_after_reweight')}
              {this.inputWrapper('storage_rules')}
            </div>
          </div>
        </>;
      break;
      case 'packaging': {
        const pkg = R.PACKAGING ?? [];
        const baseUnitName = this.unitName(R.id_unit) || (R.UNIT?.name ?? this.translate('Base unit'));
        let acc = 1;
        let accValid = true;
        const baseCounts = pkg.map((row: any) => {
          if (row._toBeDeleted_) return null;
          const q = parseFloat(row.qty_per_lower);
          if (!accValid || !isFinite(q) || q <= 0) { accValid = false; return null; }
          acc = acc * q;
          return acc;
        });
        const fmtBase = (n: number) => globalThis.hubleto.numberFormat(n, Number.isInteger(n) ? 0 : 2);
        return <>
          <div className='mb-4'>
            <h3 className='font-bold'>{this.translate('Packaging levels')}</h3>
            <p className='text-sm text-gray-500 mb-2'>
              {this.translate('Each level wraps the one above it. The base unit is the foundation (always 1).')}
            </p>
            <table className='table-default dense mt-2 w-full' style={{tableLayout: 'fixed'}}>
              <thead>
                <tr>
                  <th style={{width: '4rem'}}>{this.translate('Order')}</th>
                  <th>1 {this.translate('unit')}</th>
                  <th style={{width: '34%'}}>{this.translate('Qty per package')}</th>
                  <th style={{width: '18%'}}>{this.translate('Base units')}</th>
                  <th style={{width: '2.5rem'}}></th>
                </tr>
              </thead>
              <tbody>
                {pkg.map((item, index) => {
                  const lowerName = index === 0
                    ? baseUnitName
                    : (this.unitName(pkg[index - 1]?.id_unit) || pkg[index - 1]?.UNIT?.name || this.translate('level above'));
                  return <tr key={index} className={item._toBeDeleted_ ? 'bg-red-100 line-through' : ''}>
                    <td>
                      <div className='flex flex-col items-center'>
                        <button
                          className='btn btn-small btn-transparent'
                          disabled={index === 0}
                          onClick={() => { this.movePackaging(index, -1); }}
                        >
                          <span className='icon'><i className='fas fa-chevron-up'></i></span>
                        </button>
                        <button
                          className='btn btn-small btn-transparent'
                          disabled={index >= ((R.PACKAGING?.length ?? 0) - 1)}
                          onClick={() => { this.movePackaging(index, 1); }}
                        >
                          <span className='icon'><i className='fas fa-chevron-down'></i></span>
                        </button>
                      </div>
                    </td>
                    <td>
                      <div className='flex items-center gap-2'>
                        <span className='text-gray-500'>1</span>
                        <div className='lookup-wrap flex-1'>
                          <Lookup
                            model='Hubleto/App/Community/Products/Models/Unit'
                            customEndpointParams={{ unitCategory: FormProduct.UNIT_CATEGORY_CONTAINER }}
                            value={item.id_unit}
                            onChange={(input: any, value: any) => { this.updatePackaging(index, item, {id_unit: value}); }}
                          ></Lookup>
                        </div>
                      </div>
                    </td>
                    <td>
                      <div className='flex items-center gap-2'>
                        <div style={{width: '7rem'}}>
                          <Int
                            value={item.qty_per_lower}
                            description={{decimals: 4}}
                            onChange={(input: any, value: any) => { this.updatePackaging(index, item, {qty_per_lower: value}); }}
                          ></Int>
                        </div>
                        <span className='text-gray-500'>{lowerName}</span>
                      </div>
                    </td>
                    <td className='align-middle'>
                      {baseCounts[index] != null ? <>{fmtBase(baseCounts[index])} <span className='text-gray-500'>{baseUnitName}</span></> : '—'}
                    </td>
                    <td>
                      <button
                        className={'btn ' + (item._toBeDeleted_ ? 'btn-primary' : 'btn-danger')}
                        onClick={() => {
                          let newR = this.state.record;
                          newR.PACKAGING[index]._toBeDeleted_ = !newR.PACKAGING[index]._toBeDeleted_;
                          this.updateRecord(newR);
                        }}
                      >
                        <span className='icon'><i className={'fas ' + (item._toBeDeleted_ ? 'fa-rotate-left' : 'fa-trash')}></i></span>
                      </button>
                    </td>
                  </tr>
                })}
              </tbody>
            </table>
            <button
              className='btn btn-add mt-2'
              onClick={() => {
                let newR = R;
                if (!newR.PACKAGING) newR.PACKAGING = [];
                newR.PACKAGING.push({ id_product: { _useMasterRecordId_: true }, sort: newR.PACKAGING.length });
                this.updateRecord(newR);
              }}
            >
              <span className='icon'><i className='fas fa-plus'></i></span>
              <span className='text'>{this.translate('Add packaging level')}</span>
            </button>
          </div>
          <hr className='my-4'/>
          <h3 className='font-bold'>{this.translate('Physical package')}</h3>
          {this.inputWrapper('package_length')}
          {this.inputWrapper('package_width')}
          {this.inputWrapper('package_height')}
          {this.inputWrapper('package_volume')}
          {this.inputWrapper('package_mass')}
          {this.inputWrapper('package_discount')}
          {this.inputWrapper('package_description')}
        </>;
      }
      break;
      case 'gallery':
        return <>
          {this.inputWrapper('image_1')}
          {this.inputWrapper('image_2')}
          {this.inputWrapper('image_3')}
          {this.inputWrapper('image_4')}
          {this.inputWrapper('image_5')}
        </>;
      break;
      case 'suppliers':
        return (this.state.id < 0 ?
          <div className="badge badge-info">{this.translate("First create the product.")}</div>
        :
          <TableProductSuppliers
            uid={this.props.uid + "_table_suppliers"}
            tag="ProductSuppliers"
            parentForm={this}
            idProduct={R.id}
          />
        );
      break;

      default:
        return super.renderTab(tabUid);
      break;
    }
  }
}