import React, { Component } from 'react';
import FormExtended, { FormExtendedProps, FormExtendedState } from '@hubleto/react-ui/ext/FormExtended';
import TableProductSuppliers from './TableProductSuppliers';
import Barcode from 'react-barcode';
import Int from '@hubleto/react-ui/core/Inputs/Int';
import Lookup from '@hubleto/react-ui/core/Inputs/Lookup';
import Varchar from '@hubleto/react-ui/core/Inputs/Varchar';
import request from '@hubleto/react-ui/core/Request';

export interface FormProductProps extends FormExtendedProps {}
export interface FormProductState extends FormExtendedState {
  unitsById?: Record<string, string>,
}

export default class FormProduct<P, S> extends FormExtended<FormProductProps,FormProductState> {
  // Mirrors Models\Product::BASE_MEASURE_* (the base unit's measure + display symbol).
  static MEASURE_COUNT = 1;
  static MEASURE_MASS = 2;
  static MEASURE_VOLUME = 3;
  static MEASURE_LENGTH = 4;

  static MEASURE_SYMBOLS: Record<number, string> = { 1: 'pcs', 2: 'kg', 3: 'l', 4: 'm' };

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
        const nameById: any = {};
        Object.keys(data ?? {}).forEach((id) => { nameById[id] = data[id]?._LOOKUP ?? ''; });
        this.setState({ unitsById: nameById });
      }
    );
  }

  unitName(id: any): string {
    return (id && this.state.unitsById) ? (this.state.unitsById[id] ?? '') : '';
  }

  // What the base unit measures (count/mass/volume/length); the symbol it shows (pcs/kg/l/m).
  baseUnitMeasureType(): number {
    return Number(this.state.record?.base_measure ?? FormProduct.MEASURE_COUNT);
  }

  baseUnitSymbol(): string {
    return FormProduct.MEASURE_SYMBOLS[this.baseUnitMeasureType()] ?? 'pcs';
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

  updateBaseUnitPhysical(changedValues: any) {
    const map: any = { length: 'base_length', width: 'base_width', height: 'base_height', weight: 'base_weight' };
    const out: any = {};
    Object.keys(changedValues).forEach((key) => { out[map[key] ?? key] = changedValues[key]; });
    this.updateRecord(out);
  }

  packageVolume(physical: any): number {
    const length = parseFloat(physical.length);
    const width = parseFloat(physical.width);
    const height = parseFloat(physical.height);
    return [length, width, height].every((value) => isFinite(value) && value > 0) ? length * width * height : 0;
  }

  renderPhysicalFields(physical: any, onFieldChange: (changedValues: any) => void, dimensions: string[] = ['length', 'width', 'height'], showWeight: boolean = true, showNote: boolean = true): React.JSX.Element {
    const numberField = (field: string, label: string, unit: string) => <div>
      <label className='text-sm text-gray-500'>{this.translate(label)} ({unit})</label>
      <Int
        value={physical[field]}
        description={{decimals: 4}}
        onChange={(input: any, value: any) => { onFieldChange({[field]: value}); }}
      ></Int>
    </div>;
    const dimensionLabels: any = { length: 'Length', width: 'Width', height: 'Height' };
    const volume = this.packageVolume(physical);
    return <div className='grid grid-cols-4 gap-2 mt-1'>
      {dimensions.map((field) => <React.Fragment key={field}>{numberField(field, dimensionLabels[field], 'm')}</React.Fragment>)}
      {showWeight ? numberField('weight', 'Weight', 'kg') : null}
      <div className='col-span-4 text-sm text-gray-500'>
        {this.translate('Volume')}: <span className='font-bold'>{volume > 0 ? globalThis.hubleto.numberFormat(volume, 4) : '—'}</span> m³
        <span className='ml-1'>({this.translate('length × width × height')})</span>
      </div>
      {showNote ? <div className='col-span-4'>
        <label className='text-sm text-gray-500'>{this.translate('Package note')}</label>
        <Varchar
          value={physical.description}
          onChange={(input: any, value: any) => { onFieldChange({description: value}); }}
        ></Varchar>
      </div> : null}
    </div>;
  }

  renderBaseUnitCard(record: any): React.JSX.Element {
    const note = (text: string) => <div className='badge badge-info'>{text}</div>;
    const symbol = this.baseUnitSymbol();
    const editBaseUnit = (changedValues: any) => this.updateBaseUnitPhysical(changedValues);
    const physical: any = {
      length: record.base_length, width: record.base_width, height: record.base_height, weight: record.base_weight,
    };
    let body: React.JSX.Element;

    switch (this.baseUnitMeasureType()) {
      case FormProduct.MEASURE_MASS:
        body = note(this.translate('Measured by weight - one') + ' ' + symbol + ' ' + this.translate('has no fixed shape. Put the real box or bag size on a packaging level below.'));
        break;
      case FormProduct.MEASURE_VOLUME:
        body = note(this.translate('Measured by volume - one') + ' ' + symbol + ' ' + this.translate('has no fixed shape. Put the real container size on a packaging level below.'));
        break;
      case FormProduct.MEASURE_LENGTH:
        body = <>
          <p className='text-sm text-gray-500 mb-2'>
            {this.translate('Measured by length - one') + ' ' + symbol + ' ' + this.translate('is 1 m long, so give its cross-section and weight per') + ' ' + symbol + '.'}
          </p>
          {this.renderPhysicalFields({ ...physical, length: 1 }, editBaseUnit, ['width', 'height'], true, false)}
        </>;
        break;
      default: // MEASURE_COUNT
        body = this.renderPhysicalFields(physical, editBaseUnit, ['length', 'width', 'height'], true, false);
        break;
    }

    return <div className='mb-4'>
      <h3 className='font-bold'>{this.translate('Base unit')} ({symbol})</h3>
      <p className='text-sm text-gray-500 mb-2'>
        {this.translate('The single sellable piece - the foundation that every packaging level wraps.')}
      </p>
      {body}
    </div>;
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
              {this.inputWrapper('base_measure')}
              {this.inputWrapper('is_lot_tracked')}
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
        const allLevels = R.PACKAGING ?? [];
        const baseUnitName = this.baseUnitSymbol();

        // every packaging row is a container level now; the base unit lives in the card above
        const containers = allLevels.map((item: any, realIndex: number) => ({ item, realIndex }));

        let runningBaseUnits = 1;
        let runningValid = true;
        const baseUnitCounts = containers.map(({ item }: any) => {
          if (item._toBeDeleted_) return null;
          const qtyPerPackage = parseFloat(item.qty_per_lower);
          if (!runningValid || !isFinite(qtyPerPackage) || qtyPerPackage <= 0) { runningValid = false; return null; }
          runningBaseUnits = runningBaseUnits * qtyPerPackage;
          return runningBaseUnits;
        });
        const formatBaseUnits = (value: number) => globalThis.hubleto.numberFormat(value, Number.isInteger(value) ? 0 : 2);

        return <>
          {this.renderBaseUnitCard(R)}
          <hr className='my-4'/>
          <div className='mb-4'>
            <h3 className='font-bold'>{this.translate('Packaging levels')}</h3>
            <p className='text-sm text-gray-500 mb-2'>
              {this.translate('Each level packs several of the unit below it. The base unit is the foundation (always 1).')}
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
                {containers.map(({ item, realIndex }: any, position: number) => {
                  const lowerLevelName = position === 0
                    ? baseUnitName
                    : (this.unitName(containers[position - 1].item.id_unit) || containers[position - 1].item.UNIT?.name || this.translate('level below'));
                  return <React.Fragment key={realIndex}>
                    <tr className={item._toBeDeleted_ ? 'bg-red-100 line-through' : ''}>
                      <td>
                        <div className='flex flex-col items-center'>
                          <button
                            className='btn btn-small btn-transparent'
                            disabled={position === 0}
                            onClick={() => { this.movePackaging(realIndex, -1); }}
                          >
                            <span className='icon'><i className='fas fa-chevron-up'></i></span>
                          </button>
                          <button
                            className='btn btn-small btn-transparent'
                            disabled={position >= (containers.length - 1)}
                            onClick={() => { this.movePackaging(realIndex, 1); }}
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
                              value={item.id_unit}
                              onChange={(input: any, value: any) => { this.updatePackaging(realIndex, item, {id_unit: value}); }}
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
                              onChange={(input: any, value: any) => { this.updatePackaging(realIndex, item, {qty_per_lower: value}); }}
                            ></Int>
                          </div>
                          <span className='text-gray-500'>{lowerLevelName}</span>
                        </div>
                      </td>
                      <td className='align-middle'>
                        {baseUnitCounts[position] != null ? <>{formatBaseUnits(baseUnitCounts[position])} <span className='text-gray-500'>{baseUnitName}</span></> : '—'}
                      </td>
                      <td>
                        <button
                          className={'btn ' + (item._toBeDeleted_ ? 'btn-primary' : 'btn-danger')}
                          onClick={() => {
                            let newR = this.state.record;
                            newR.PACKAGING[realIndex]._toBeDeleted_ = !newR.PACKAGING[realIndex]._toBeDeleted_;
                            this.updateRecord(newR);
                          }}
                        >
                          <span className='icon'><i className={'fas ' + (item._toBeDeleted_ ? 'fa-rotate-left' : 'fa-trash')}></i></span>
                        </button>
                      </td>
                    </tr>
                    <tr className={item._toBeDeleted_ ? 'bg-red-100' : ''}>
                      <td></td>
                      <td colSpan={4}>
                        {this.renderPhysicalFields(item, (changedValues: any) => this.updatePackaging(realIndex, item, changedValues), ['length', 'width', 'height'], false)}
                      </td>
                    </tr>
                  </React.Fragment>;
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