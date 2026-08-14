import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import TableProducts from './TableProducts';

export interface FormCategoryProps extends FormProps {}

const componentName = 'FormCategory'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Products';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormCategoryProps) => {
  return <div className='flex gap-2'>
    <div className='grow'>
      <Input field='name' customInputProps={{cssClass: 'text-2xl'}} />
      <Input field='id_parent' />
      <Input field='short_description' />
      <Input field='url_slug' />
    </div>
    <div className='grow'>
      <Input field='photo_1' />
      <Input field='photo_2' />
      <Input field='photo_3' />
      <Input field='photo_4' />
      <Input field='photo_5' />
    </div>
  </div>;
}

/** TabDescription */
const TabDescription = (props: FormCategoryProps) => {
  return <Input field='long_description' />;
}

/** TabProducts */
const TabProducts = (props: FormCategoryProps) => {
  const form = React.useContext(FormMetaContext);

  return (props.id < 0 ?
    <div className="badge badge-info">{T.translate("First create the category.")}</div>
  :
    <TableProducts
      uid={props.uid + "_table_category_products"}
      tag="table_category_products"
      parentForm={form}
      idCategory={props.id}
    />
  );
}

/** FormCategory */
const FormCategory = (props: FormCategoryProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Category'}
    urlSlug='products/categories'
    endpointParams={{}}
    title={{field: 'title', sub: T.translate('Category')}}
    tabs={{
      default: {title: <b>{T.translate('Category')}</b>, content: () => <TabDefault {...props} />},
      description: {title: T.translate('Description'), content: () => <TabDescription {...props} />},
      products: {title: T.translate('Products'), content: () => <TabProducts {...props} />},
    }}
    {...props}
  ></Form>;
}

export default FormCategory;

