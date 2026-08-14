import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';

export interface FormProductSupplierProps extends FormProps {}

const componentName = 'FormProductSupplier'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Products';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** FormProductSupplier */
const FormProductSupplier = (props: FormProductSupplierProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/ProductSupplier'}
    urlSlug='products/suppliers'
    title={{fields: ['supplier_product_code', 'supplier_product_name'], sub: T.translate('Product supplier')}}
    {...props}
  ></Form>;
}

export default FormProductSupplier;
