import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import request from '@hubleto/react-ui/core/Request';

const componentName = 'EffectivePriceLists'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/PriceLists';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

export interface EffectivePriceListsProps {
  idCustomer?: number,
}

/** Price lists that apply to a customer, best match first. */
const EffectivePriceLists = (props: EffectivePriceListsProps) => {
  const [lists, setLists] = React.useState<Array<any>>([]);

  React.useEffect(() => {
    if (!(props.idCustomer > 0)) return;
    request.get('price-lists/api/get-price-lists-for-party', {idCustomer: props.idCustomer}, (data: any) => setLists(data.priceLists ?? []));
  }, [props.idCustomer]);

  return <div className='bg-slate-50 p-2 mb-2'>
    <b>{T.translate('Effective price lists')}</b>
    {lists.length == 0 ? <div className='text-sm text-gray-500'>{T.translate('No price list is assigned to this customer.')}</div> : <ol className='list-decimal ml-4 text-sm'>
      {lists.map((list: any) => <li key={list.idPriceList}>
        <a href={globalThis.hubleto.config.projectUrl + '/price-lists/' + list.idPriceList} target='_blank'>{list.code} {list.name}</a> <span className='text-gray-500'>({list.via})</span>
      </li>)}
    </ol>}
  </div>;
}

export default EffectivePriceLists;
