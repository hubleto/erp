import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormShopProps extends FormProps {}

const componentName = 'FormShop'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Shops';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormShopProps) => {
  return <div className='w-full flex gap-2 flex-col md:flex-row'>
    <div className='flex-1 border-r border-gray-100'>
      <Input field='name' customInputProps={{cssClass: 'text-2xl'}} />
      <Input field='address' customInputProps={{cssClass: 'text-2xl'}} />
      <Input field='short_description' />
      <Input field='long_description' />
    </div>
    <div className='flex-1'>
      <Input field='photo_1' />
      <Input field='photo_2' />
      <Input field='photo_3' />
      <Input field='photo_4' />
      <Input field='photo_5' />
    </div>
  </div>;
}

/** FormShop */
const FormShop = (props: FormShopProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Shop'}
    urlSlug='shops'
    endpointParams={{}}
    // onAfterFormInitialized={(form: any) => {}}
    // renderTitle={(): React.JSX.Element => { return <></>; }
    title={{field: 'address', sub: T.translate('Shop')}}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormShop;
