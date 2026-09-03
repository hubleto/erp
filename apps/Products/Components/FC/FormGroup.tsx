import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form from '@hubleto/react-ui/components/fc/Form';

const componentName = 'FormGroup'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Products';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** FormGroup */
const FormGroup = (props: FormProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Group'}
    urlSlug='products/groups'
    endpointParams={{}}
    title={{field: 'title', sub: T.translate('Group')}}
    {...props}
  ></Form>;
}

export default FormGroup;
