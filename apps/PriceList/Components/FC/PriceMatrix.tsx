import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import request from '@hubleto/react-ui/core/Request';
import LookupInput from '@hubleto/react-ui/components/fc/Inputs/Lookup';

const componentName = 'PriceMatrix'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/PriceLists';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

export interface PriceMatrixProps {
  idPriceList?: number,
}

/** PriceMatrix: fixed prices of products (rows) by quantity tiers (columns). Editing a cell saves it immediately. */
const PriceMatrix = (props: PriceMatrixProps) => {
  const [idPriceList, setIdPriceList] = React.useState<number>(props.idPriceList ?? 0);
  const [tiers, setTiers] = React.useState<Array<number>>([]);
  const [rows, setRows] = React.useState<Array<any>>([]);
  const [newProduct, setNewProduct] = React.useState<number>(0);
  const [error, setError] = React.useState<string>('');

  const load = (id: number) => {
    if (!(id > 0)) { setTiers([]); setRows([]); return; }
    request.get('price-lists/api/get-matrix', {idPriceList: id}, (data: any) => {
      setTiers(data.tiers ?? []);
      setRows(data.rows ?? []);
    });
  };

  React.useEffect(() => { load(idPriceList); }, [idPriceList]);

  const save = (idProduct: number, minQty: number, value: string) => {
    request.post('price-lists/api/set-matrix-price', {idPriceList: idPriceList, idProduct: idProduct, minQty: minQty, value: value}, {}, (data: any) => {
      if (data.status == 'success') { setError(''); load(idPriceList); }
      else setError(data.message ?? '');
    });
  };

  const addTier = () => {
    const input = globalThis.window.prompt(T.translate('Minimum quantity of the new tier'));
    const qty = parseFloat(input ?? '');
    if (!isNaN(qty) && !tiers.includes(qty)) setTiers([...tiers, qty].sort((a, b) => a - b));
  };

  return <div className='flex flex-col gap-2'>
    <div style={{maxWidth: '30em'}}>
      <label className='font-bold'>{T.translate('Price list')}</label>
      <LookupInput model={parentApp + '/Models/PriceList'} value={idPriceList} onChange={(input: any, value: any) => setIdPriceList(value)}></LookupInput>
    </div>

    {error ? <div className='alert alert-danger'>{error}</div> : null}

    {idPriceList > 0 ? <>
      <div style={{overflowX: 'auto'}}>
        <table className='table-default dense'>
          <thead>
            <tr>
              <th>{T.translate('Product')}</th>
              {tiers.map((tier) => <th key={tier}>{T.translate('from')} {globalThis.hubleto.numberFormat(tier, 0)}</th>)}
            </tr>
          </thead>
          <tbody>
            {rows.map((row) => <tr key={row.idProduct}>
              <td>{row.productName}</td>
              {tiers.map((tier) => {
                const cell = row.prices[String(tier)];
                return <td key={tier}>
                  <input
                    className='input'
                    style={{width: '8em'}}
                    type='number'
                    step='any'
                    defaultValue={cell ? cell.value : ''}
                    key={row.idProduct + '_' + tier + '_' + (cell ? cell.value : '')}
                    onBlur={(e) => { if ((cell ? String(cell.value) : '') !== e.target.value) save(row.idProduct, tier, e.target.value); }}
                  />
                </td>;
              })}
            </tr>)}
          </tbody>
        </table>
      </div>
      <div className='flex gap-2 items-center'>
        <button className='btn btn-transparent' onClick={addTier}>
          <span className='icon'><i className='fas fa-plus'></i></span>
          <span className='text'>{T.translate('Add quantity tier')}</span>
        </button>
        <div style={{minWidth: '20em'}}>
          <LookupInput model='Hubleto/App/Community/Products/Models/Product' value={newProduct} onChange={(input: any, value: any) => {
            if (value > 0 && tiers.length > 0) { setNewProduct(0); save(value, tiers[0], '0'); }
            else if (value > 0) { setNewProduct(0); save(value, 0, '0'); }
          }}></LookupInput>
        </div>
        <span className='text-xs text-gray-500'>{T.translate('Pick a product to add a row.')}</span>
      </div>
    </> : null}
  </div>;
}

export default PriceMatrix;
