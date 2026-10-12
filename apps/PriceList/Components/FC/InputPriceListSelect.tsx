import React from 'react';
import LookupInput from '@hubleto/react-ui/components/fc/Inputs/Lookup';

export interface InputPriceListSelectProps {
  value?: number,
  onChange?: (input: any, value: any) => void,
  cssClass?: string,
}

/**
 * Select of a price list for use in forms of other apps:
 *   import InputPriceListSelect from '@hubleto/apps/PriceList/Components/FC/InputPriceListSelect';
 */
const InputPriceListSelect = (props: InputPriceListSelectProps) => {
  return <LookupInput
    model='Hubleto/App/Community/PriceLists/Models/PriceList'
    value={props.value}
    cssClass={props.cssClass}
    onChange={props.onChange}
  ></LookupInput>;
}

export default InputPriceListSelect;
