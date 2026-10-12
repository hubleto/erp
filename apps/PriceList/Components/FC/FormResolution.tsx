import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { type FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form from '@hubleto/react-ui/components/fc/Form';

const componentName = 'FormResolution'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Custom/PriceList';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** FormResolution */
const FormResolution = (props: FormProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Resolution'}
    urlSlug='price-lists/resolutions'
    endpointParams={{}}
    title={{field: 'id', sub: T.translate('Resolutions')}}
    {...props}
  ></Form>;
}

export default FormResolution;
