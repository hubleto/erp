import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import request from '@hubleto/react-ui/core/Request';
import LookupInput from '@hubleto/react-ui/components/fc/Inputs/Lookup';

const componentName = 'PriceSimulator'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Custom/PriceList';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** PriceSimulator: shows which price the resolver chooses and why. */
const PriceSimulator = () => {
  const [query, setQuery] = React.useState<any>({
    idProduct: 0, qty: 1, idCustomer: 0, idShop: 0, idCurrency: 0, date: new Date().toISOString().substring(0, 10), type: 'sales',
  });
  const [data, setData] = React.useState<any>(null);
  const [lists, setLists] = React.useState<Array<any>>([]);

  const setValue = (key: string, value: any) => setQuery({...query, [key]: value});

  const simulate = () => {
    request.get('price-lists/api/explain-price', query, (response: any) => setData(response));
    request.get('price-lists/api/get-price-lists-for-party', query, (response: any) => setLists(response.priceLists ?? []));
  };

  const result = data?.result;

  return <div className='flex-dyn gap-4'>
    <div className='flex-1 flex flex-col gap-2'>
      <div>
        <label className='font-bold'>{T.translate('Product')}</label>
        <LookupInput model='Hubleto/App/Community/Products/Models/Product' value={query.idProduct} onChange={(input: any, value: any) => setValue('idProduct', value)}></LookupInput>
      </div>
      <div>
        <label className='font-bold'>{T.translate('Customer')}</label>
        <LookupInput model='Hubleto/App/Community/Customers/Models/Customer' value={query.idCustomer} onChange={(input: any, value: any) => setValue('idCustomer', value)}></LookupInput>
      </div>
      <div>
        <label className='font-bold'>{T.translate('Shop')}</label>
        <LookupInput model='Hubleto/App/Community/Shops/Models/Shop' value={query.idShop} onChange={(input: any, value: any) => setValue('idShop', value)}></LookupInput>
      </div>
      <div>
        <label className='font-bold'>{T.translate('Currency')}</label>
        <LookupInput model='Hubleto/App/Community/Settings/Models/Currency' value={query.idCurrency} onChange={(input: any, value: any) => setValue('idCurrency', value)}></LookupInput>
      </div>
      <div>
        <label className='font-bold'>{T.translate('Quantity')}</label>
        <input className='input' type='number' value={query.qty} onChange={(e) => setValue('qty', e.target.value)} />
      </div>
      <div>
        <label className='font-bold'>{T.translate('Date')}</label>
        <input className='input' type='date' value={query.date} onChange={(e) => setValue('date', e.target.value)} />
      </div>
      <div>
        <label className='font-bold'>{T.translate('Type')}</label>
        <select className='input' value={query.type} onChange={(e) => setValue('type', e.target.value)}>
          <option value='sales'>{T.translate('Sales')}</option>
          <option value='purchase'>{T.translate('Purchase')}</option>
        </select>
      </div>
      <button className='btn btn-primary' onClick={simulate} disabled={!(query.idProduct > 0)}>
        <span className='icon'><i className='fas fa-flask'></i></span>
        <span className='text'>{T.translate('Simulate')}</span>
      </button>
    </div>

    <div className='flex-1 flex flex-col gap-2'>
      {result ? <>
        <div className='bg-slate-50 p-2'>
          <div className='text-[2em] font-bold'>{globalThis.hubleto.currencyFormat(result.unitPrice, 4)}</div>
          <table className='table-default dense w-full'>
            <tbody>
              <tr><td>{T.translate('Source')}</td><td>{result.source}</td></tr>
              <tr><td>{T.translate('Price list')}</td><td>{result.idPriceList > 0 ? <a href={globalThis.hubleto.config.projectUrl + '/price-lists/' + result.idPriceList} target='_blank'>#{result.idPriceList}</a> : '-'}</td></tr>
              <tr><td>{T.translate('Base price')}</td><td>{globalThis.hubleto.currencyFormat(result.basePrice, 4)}</td></tr>
              <tr><td>{T.translate('Discount')}</td><td>{globalThis.hubleto.numberFormat(result.discountPercent, 2)} %</td></tr>
              <tr><td>{T.translate('VAT')}</td><td>{globalThis.hubleto.numberFormat(result.vatPercent, 2)} % {result.vatNote}</td></tr>
              <tr><td>{T.translate('Price excluding VAT')}</td><td>{globalThis.hubleto.currencyFormat(result.unitPriceExclVat, 4)}</td></tr>
              <tr><td>{T.translate('Price including VAT')}</td><td>{globalThis.hubleto.currencyFormat(result.unitPriceInclVat, 4)}</td></tr>
            </tbody>
          </table>
        </div>
        <div>
          <b>{T.translate('Why this price')}</b>
          <ol className='list-decimal ml-4 text-sm'>
            {(data.trace ?? []).map((line: string, index: number) => <li key={index}>{line}</li>)}
          </ol>
        </div>
      </> : null}
      {lists.length > 0 ? <div>
        <b>{T.translate('Applicable price lists (best first)')}</b>
        <ul className='text-sm'>
          {lists.map((list: any) => <li key={list.idPriceList}>
            <a href={globalThis.hubleto.config.projectUrl + '/price-lists/' + list.idPriceList} target='_blank'>{list.code} {list.name}</a> <span className='text-gray-500'>({list.via})</span>
          </li>)}
        </ul>
      </div> : null}
    </div>
  </div>;
}

export default PriceSimulator;
