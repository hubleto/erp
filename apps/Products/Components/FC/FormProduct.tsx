import React, { useEffect, useState } from 'react'
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import TableProductSuppliers from './TableProductSuppliers';
import Barcode from 'react-barcode';
import { type FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Translator from '@hubleto/react-ui/core/Translator';
import request from '@hubleto/react-ui/core/Request';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import Int from '@hubleto/react-ui/components/fc/Inputs/Int';
import Lookup from '@hubleto/react-ui/components/fc/Inputs/Lookup';
import Varchar from '@hubleto/react-ui/components/fc/Inputs/Varchar';
import { useRecord, useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';

const componentName = 'FormProduct'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Products';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

// Mirrors Models\Product::BASE_MEASURE_* (the base unit's measure + display symbol).
const MEASURE_COUNT = 1;
const MEASURE_MASS = 2;
const MEASURE_VOLUME = 3;
const MEASURE_LENGTH = 4;

const MEASURE_SYMBOLS: Record<number, string> = { 1: 'pcs', 2: 'kg', 3: 'l', 4: 'm' };

const baseUnitMeasure = (record: any): number => Number(record.base_measure ?? MEASURE_COUNT);

const baseUnitSymbol = (record: any): string => MEASURE_SYMBOLS[baseUnitMeasure(record)] ?? 'pcs';

const packageVolume = (physical: any): number => {
  const length = parseFloat(physical.length);
  const width = parseFloat(physical.width);
  const height = parseFloat(physical.height);
  const allPositive = [length, width, height].every((value) => isFinite(value) && value > 0);
  return allPositive ? length * width * height : 0;
};

// Gross minus net is the sales packaging - the bag or bucket the goods never leave. Nothing
// stores it, so showing it back is the only way a typo in either figure becomes visible.
const salesPackagingWeight = (physical: any): number | null => {
  const gross = parseFloat(physical.weight);
  const net = parseFloat(physical.net_weight);
  if (!isFinite(gross) || !isFinite(net) || gross <= 0 || net <= 0) return null;
  return gross - net;
};

const DIMENSION_LABELS: Record<string, string> = { length: 'Length', width: 'Width', height: 'Height' };

/** Dimensions, weights and an optional note - used for the base unit and for every packaging level.
 * weightFields: baseUnit = gross + net of the goods, tare = the empty container, none = no weight. */
const PhysicalFields = (props: {
  physical: any,
  onFieldChange: (changedValues: any) => void,
  dimensions?: string[],
  weightFields?: 'baseUnit' | 'tare' | 'none',
  showNote?: boolean,
}) => {
  const dimensions = props.dimensions ?? ['length', 'width', 'height'];
  const weightFields = props.weightFields ?? 'baseUnit';
  const showNote = props.showNote ?? true;
  const volume = packageVolume(props.physical);
  const salesPackaging = salesPackagingWeight(props.physical);

  // gross + net make five fields next to three dimensions, so the row widens instead of wrapping
  let weightFieldCount = 0;
  if (weightFields === 'baseUnit') weightFieldCount = 2;
  if (weightFields === 'tare') weightFieldCount = 1;
  const isWide = dimensions.length + weightFieldCount >= 5;
  const gridClass = isWide ? 'grid-cols-5' : 'grid-cols-4';
  const fullRowClass = isWide ? 'col-span-5' : 'col-span-4';

  const numberField = (field: string, label: string, unit: string) => <div key={field}>
    <label className='text-sm text-gray-500'>{T.translate(label)} ({unit})</label>
    <Int
      value={props.physical[field] ?? ''}
      description={{decimals: 4}}
      onChange={(_input: any, value: any) => props.onFieldChange({[field]: value})}
    />
  </div>;

  return <div className={'grid ' + gridClass + ' gap-2 mt-1'}>
    {dimensions.map((field) => numberField(field, DIMENSION_LABELS[field], 'm'))}
    {weightFields === 'baseUnit' ? numberField('weight', 'Gross weight', 'kg') : null}
    {weightFields === 'baseUnit' ? numberField('net_weight', 'Net weight', 'kg') : null}
    {weightFields === 'tare' ? numberField('weight', 'Tare weight for this product', 'kg') : null}
    {dimensions.length > 0 ? <div className={fullRowClass + ' text-sm text-gray-500'}>
      {T.translate('Volume')}: <span className='font-bold'>{volume > 0 ? globalThis.hubleto.numberFormat(volume, 4) : '—'}</span> m³
      <span className='ml-1'>({T.translate('length × width × height')})</span>
    </div> : null}
    {/* the number stays on screen when it goes negative - how far below zero is the size of the typo */}
    {weightFields === 'baseUnit' && salesPackaging !== null ? <div className={fullRowClass + ' flex items-center gap-2'}>
      <div className='text-sm text-gray-500'>
        {T.translate('Sales packaging tare')}: <span className='font-bold'>{globalThis.hubleto.numberFormat(salesPackaging, 4)}</span> kg
        <span className='ml-1'>({T.translate('gross − net')})</span>
      </div>
      {salesPackaging < 0 ? <div className='badge badge-danger'>
        {T.translate('Net weight is higher than gross weight.')}
      </div> : null}
    </div> : null}
    {showNote ? <div className={fullRowClass}>
      <label className='text-sm text-gray-500'>{T.translate('Package note')}</label>
      <Varchar
        value={props.physical.description}
        onChange={(_input: any, value: any) => props.onFieldChange({description: value})}
      />
    </div> : null}
  </div>;
};

/** The single sellable piece - its size depends on what the base unit measures. */
const BaseUnitCard = () => {
  const form = React.useContext(FormMetaContext);
  const record = useRecord();
  const symbol = baseUnitSymbol(record);

  // the card edits generic length/width/height/weight; the record stores them as base_*
  const editBaseUnit = (changedValues: any) => {
    const columnByField: Record<string, string> = { length: 'base_length', width: 'base_width', height: 'base_height', weight: 'base_weight', net_weight: 'base_net_weight' };
    const changes: any = {};
    for (const field of Object.keys(changedValues)) {
      changes[columnByField[field] ?? field] = changedValues[field];
    }
    form.changeRecord(changes);
  };

  const physical = {
    length: record.base_length,
    width: record.base_width,
    height: record.base_height,
    weight: record.base_weight,
    net_weight: record.base_net_weight,
  };
  const note = (text: string) => <div className='badge badge-info'>{text}</div>;

  let body: React.JSX.Element;
  switch (baseUnitMeasure(record)) {
    case MEASURE_MASS:
      // one kg weighs one kg, so nothing to enter - the warehouse derives the load from the quantity
      body = note(T.translate('Measured by weight - one') + ' ' + symbol + ' ' + T.translate('has no fixed shape and weighs 1 kg, so warehouse load comes straight from the quantity. Put the real box or bag size on a packaging level below.'));
      break;
    case MEASURE_VOLUME:
      body = <>
        {note(T.translate('Measured by volume - one') + ' ' + symbol + ' ' + T.translate('has no fixed shape. Put the real container size on a packaging level below.'))}
        <p className='text-sm text-gray-500 mt-2'>
          {T.translate('Weight cannot be derived from volume, so give the weight of one') + ' ' + symbol + ' ' + T.translate('or the warehouse counts this product as weightless.')}
        </p>
        <PhysicalFields physical={physical} onFieldChange={editBaseUnit} dimensions={[]} showNote={false} />
      </>;
      break;
    case MEASURE_LENGTH:
      body = <>
        <p className='text-sm text-gray-500 mb-2'>
          {T.translate('Measured by length - one') + ' ' + symbol + ' ' + T.translate('is 1 m long, so give its cross-section and weight per') + ' ' + symbol + '.'}
        </p>
        <PhysicalFields physical={{...physical, length: 1}} onFieldChange={editBaseUnit} dimensions={['width', 'height']} showNote={false} />
      </>;
      break;
    default: // MEASURE_COUNT
      body = <PhysicalFields physical={physical} onFieldChange={editBaseUnit} showNote={false} />;
      break;
  }

  return <div className='mb-4'>
    <h3 className='font-bold'>{T.translate('Base unit')} ({symbol})</h3>
    <p className='text-sm text-gray-500 mb-2'>
      {T.translate('The single sellable piece - the foundation that every packaging level wraps.')}
    </p>
    {body}
  </div>;
};

/** Packaging levels: each one packs several of the level below it, the first one packs base units. */
const PackagingLevels = () => {
  const form = React.useContext(FormMetaContext);
  const record = useRecord();
  const [unitNameById, setUnitNameById] = useState<Record<string, string>>({});
  const [unitTareById, setUnitTareById] = useState<Record<string, number>>({});

  // unit names for the "N x <level below>" label and unit tares for the computed weights
  useEffect(() => {
    request.post(
      globalThis.hubleto.config.defaultLookupEndpoint ?? 'api/record/lookup',
      { model: parentApp + '/Models/Unit', search: '', __IS_AJAX__: '1' },
      {},
      (data: any) => {
        const nameById: Record<string, string> = {};
        const tareById: Record<string, number> = {};
        for (const unit of data ?? []) {
          nameById[unit.id] = unit._LOOKUP ?? '';
          tareById[unit.id] = parseFloat(unit.tare_weight);
        }
        setUnitNameById(nameById);
        setUnitTareById(tareById);
      }
    );
  }, []);

  const levels: any[] = record.PACKAGING ?? [];
  const symbol = baseUnitSymbol(record);

  const changeLevels = (newLevels: any[]) => form.changeRecord({PACKAGING: newLevels});

  const updateLevel = (index: number, changedValues: any) => {
    const newLevels = [...levels];
    newLevels[index] = {...levels[index], ...changedValues, id_product: {_useMasterRecordId_: true}};
    changeLevels(newLevels);
  };

  const moveLevel = (index: number, direction: number) => {
    const target = index + direction;
    if (target < 0 || target >= levels.length) return;
    const newLevels = [...levels];
    const moved = newLevels[index];
    newLevels[index] = newLevels[target];
    newLevels[target] = moved;
    for (let position = 0; position < newLevels.length; position++) {
      newLevels[position] = {...newLevels[position], sort: position};
    }
    changeLevels(newLevels);
  };

  const toggleDelete = (index: number) => updateLevel(index, {_toBeDeleted_: !levels[index]._toBeDeleted_});

  const addLevel = () => changeLevels([...levels, {id_product: {_useMasterRecordId_: true}, sort: levels.length}]);

  // how many base units one package of each level holds; unknown once any level below is incomplete
  const baseUnitCounts: Array<number | null> = [];
  let runningBaseUnits = 1;
  let chainComplete = true;
  for (const level of levels) {
    const qtyPerLower = parseFloat(level.qty_per_lower);
    if (level._toBeDeleted_) {
      baseUnitCounts.push(null);
      continue;
    }
    if (!chainComplete || !isFinite(qtyPerLower) || qtyPerLower <= 0) {
      chainComplete = false;
      baseUnitCounts.push(null);
      continue;
    }
    runningBaseUnits = runningBaseUnits * qtyPerLower;
    baseUnitCounts.push(runningBaseUnits);
  }
  const formatBaseUnits = (value: number) => globalThis.hubleto.numberFormat(value, Number.isInteger(value) ? 0 : 2);

  // What one empty container of this level weighs: the per-product value, or the container type's default.
  const levelOwnTare = (level: any): number | null => {
    const ownWeight = parseFloat(level.weight);
    if (isFinite(ownWeight) && ownWeight > 0) return ownWeight;
    const unitTare = unitTareById[level.id_unit];
    if (isFinite(unitTare) && unitTare > 0) return unitTare;
    return null;
  };

  // A full container weighs its own packaging plus everything nested inside it. The base unit is
  // not counted - its wrapper is already inside the base unit gross weight.
  const cumulativeTares: Array<number | null> = [];
  let runningTare: number | null = 0;
  for (let position = 0; position < levels.length; position++) {
    const level = levels[position];
    const ownTare = levelOwnTare(level);
    const qtyPerLower = parseFloat(level.qty_per_lower);
    if (level._toBeDeleted_) {
      cumulativeTares.push(null);
      continue;
    }
    if (runningTare === null || ownTare === null) {
      runningTare = null;
      cumulativeTares.push(null);
      continue;
    }
    if (position === 0) {
      runningTare = ownTare;
    } else if (!isFinite(qtyPerLower) || qtyPerLower <= 0) {
      runningTare = null;
    } else {
      runningTare = ownTare + qtyPerLower * runningTare;
    }
    cumulativeTares.push(runningTare);
  }

  // What a full container of each level puts on the scale - the same sum a location's weight uses.
  const baseUnitGross = parseFloat(record.base_weight);
  const fullContainerWeights: Array<number | null> = [];
  for (let position = 0; position < levels.length; position++) {
    const baseUnits = baseUnitCounts[position];
    const tare = cumulativeTares[position];
    const known = baseUnits != null && tare != null && isFinite(baseUnitGross) && baseUnitGross > 0;
    fullContainerWeights.push(known ? (baseUnits as number) * baseUnitGross + (tare as number) : null);
  }

  // The full container's weight when the base unit weight is known, otherwise just the packaging.
  const renderLevelWeight = (position: number): React.JSX.Element | null => {
    const fullWeight = fullContainerWeights[position];
    const tare = cumulativeTares[position];

    if (fullWeight != null) {
      return <div className='text-sm text-gray-500 mt-1'>
        {T.translate('Full container weighs')}: <span className='font-bold'>{globalThis.hubleto.numberFormat(fullWeight, 4)}</span> kg
        <span className='ml-1'>
          ({formatBaseUnits(baseUnitCounts[position] as number)} {symbol} × {globalThis.hubleto.numberFormat(baseUnitGross, 4)} kg
          + {globalThis.hubleto.numberFormat(tare, 4)} kg {T.translate('tare')})
        </span>
      </div>;
    }

    if (tare != null) {
      return <div className='text-sm text-gray-500 mt-1'>
        {T.translate('Tare incl. inner packaging')}: <span className='font-bold'>{globalThis.hubleto.numberFormat(tare, 4)}</span> kg
      </div>;
    }

    return null;
  };

  const lowerLevelName = (position: number): string => {
    if (position === 0) return symbol;
    const levelBelow = levels[position - 1];
    return unitNameById[levelBelow.id_unit] || levelBelow.UNIT?.name || T.translate('level below');
  };

  return <div className='mb-4'>
    <h3 className='font-bold'>{T.translate('Packaging levels')}</h3>
    <p className='text-sm text-gray-500 mb-2'>
      {T.translate('Each level packs several of the unit below it. The base unit is the foundation (always 1).')}
    </p>
    <table className='table-default dense mt-2 w-full' style={{tableLayout: 'fixed'}}>
      <thead>
        <tr>
          <th style={{width: '4rem'}}>{T.translate('Order')}</th>
          <th>1 {T.translate('unit')}</th>
          <th style={{width: '34%'}}>{T.translate('Qty per package')}</th>
          <th style={{width: '18%'}}>{T.translate('Base units')}</th>
          <th style={{width: '2.5rem'}}></th>
        </tr>
      </thead>
      <tbody>
        {levels.map((level: any, position: number) => <React.Fragment key={position}>
          <tr className={level._toBeDeleted_ ? 'bg-red-100 line-through' : ''}>
            <td>
              <div className='flex flex-col items-center'>
                <button className='btn btn-small btn-transparent' disabled={position === 0} onClick={() => moveLevel(position, -1)}>
                  <span className='icon'><i className='fas fa-chevron-up'></i></span>
                </button>
                <button className='btn btn-small btn-transparent' disabled={position >= levels.length - 1} onClick={() => moveLevel(position, 1)}>
                  <span className='icon'><i className='fas fa-chevron-down'></i></span>
                </button>
              </div>
            </td>
            <td>
              <div className='flex items-center gap-2'>
                <span className='text-gray-500'>1</span>
                <div className='lookup-wrap flex-1'>
                  <Lookup
                    model={parentApp + '/Models/Unit'}
                    value={level.id_unit}
                    onChange={(_input: any, value: any) => updateLevel(position, {id_unit: value})}
                  />
                </div>
              </div>
            </td>
            <td>
              <div className='flex items-center gap-2'>
                <div style={{width: '7rem'}}>
                  <Int
                    value={level.qty_per_lower ?? ''}
                    description={{decimals: 4}}
                    onChange={(_input: any, value: any) => updateLevel(position, {qty_per_lower: value})}
                  />
                </div>
                <span className='text-gray-500'>{lowerLevelName(position)}</span>
              </div>
            </td>
            <td className='align-middle'>
              {baseUnitCounts[position] != null
                ? <>{formatBaseUnits(baseUnitCounts[position] as number)} <span className='text-gray-500'>{symbol}</span></>
                : '—'}
            </td>
            <td>
              <button className={'btn ' + (level._toBeDeleted_ ? 'btn-primary' : 'btn-danger')} onClick={() => toggleDelete(position)}>
                <span className='icon'><i className={'fas ' + (level._toBeDeleted_ ? 'fa-rotate-left' : 'fa-trash')}></i></span>
              </button>
            </td>
          </tr>
          <tr className={level._toBeDeleted_ ? 'bg-red-100' : ''}>
            <td></td>
            <td colSpan={4}>
              <PhysicalFields physical={level} onFieldChange={(changedValues: any) => updateLevel(position, changedValues)} weightFields='tare' />
              {renderLevelWeight(position)}
            </td>
          </tr>
        </React.Fragment>)}
      </tbody>
    </table>
    <button className='btn btn-add mt-2' onClick={addLevel}>
      <span className='icon'><i className='fas fa-plus'></i></span>
      <span className='text'>{T.translate('Add packaging level')}</span>
    </button>
  </div>;
};

/** TabDefault */
const TabDefault = (props: FormProps) => {
  const ean = useRecordField('ean');

  return <div className='grid grid-cols-2 gap-2'>
    <div className='border-r border-gray-200'>
      <div className='flex gap-2'>
        <div className='flex grow'><Input field='ean' /></div>
        <div className='flex grow'><Barcode value={ean} height={30} /></div>
      </div>
      <Input field='name' customInputProps={{cssClass: 'text-2xl'}} />
      <Input field='is_on_sale' customInputProps={{yesText: T.translate('On sale')}} />
      <Input field='sales_price' />
      <Input field='id_group' />
      <Input field='id_category' />
      <Input field='vat' />
      <Input field='margin' />
      <Input field='base_measure' />
      <Input field='is_lot_tracked' />
      <Input field='description' />
      <Input field='is_single_order_possible' customInputProps={{yesText: T.translate('Single unit order possible')}} />
      <Input field='show_price' customInputProps={{yesText: T.translate('Show price to customer')}} />
      <Input field='needs_reordering' customInputProps={{yesText: T.translate('Needs reordering')}} />
    </div>
    <div className=''>
      <Input field='type' />
      <Input field='invoicing_policy' />
      <Input field='sale_ended' />
      <Input field='price_after_reweight' />
      <Input field='storage_rules' />
    </div>
  </div>;
}

/** TabPackaging */
const TabPackaging = (props: FormProps) => {
  return <>
    <BaseUnitCard />
    <hr className='my-4'/>
    <PackagingLevels />
  </>;
}

/** TabGallery */
const TabGallery = (props: FormProps) => {
  return <>
    <Input field='image_1' />
    <Input field='image_2' />
    <Input field='image_3' />
    <Input field='image_4' />
    <Input field='image_5' />
  </>;
}

/** TabSuppliers */
const TabSuppliers = (props: FormProps) => {
  const form = React.useContext(FormMetaContext);

  return (form.id < 0 ?
    <div className="badge badge-info">{T.translate("First create the product.")}</div>
  :
    <TableProductSuppliers
      uid={props.uid + "_table_suppliers"}
      tag="ProductSuppliers"
      parentForm={form}
      idProduct={form.id}
    />
  );
}

const FormProduct = (props: FormProps) => {
  return <Form
    {...props}
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Product'}
    urlSlug='products'
    title={{fields: ['ean', 'name'], sub: T.translate('Product')}}
    endpointParams={{...(props.endpointParams ?? {}), saveRelations: ['PACKAGING']}}
    tabs={{
      default: {title: <b>{T.translate('Product')}</b>, content: () => <TabDefault {...props} />},
      packaging: {title: T.translate('Packaging'), content: () => <TabPackaging {...props} />},
      gallery: {title: T.translate('Gallery'), content: () => <TabGallery {...props} />},
      suppliers: {title: T.translate('Suppliers'), content: () => <TabSuppliers {...props} />},
    }}
  />;
}

export default FormProduct;
