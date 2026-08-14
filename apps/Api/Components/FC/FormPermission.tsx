import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormPermissionProps extends FormProps {}

const componentName = 'FormPermission'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Api';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormPermissionProps) => {
  return <>
    <Input field='id_key' />
    <Input field='app' />
    <Input field='controller' />
  </>;
}

/** FormPermission */
const FormPermission = (props: FormPermissionProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Permission'}
    urlSlug='api/permissions'
    title={{field: 'id', sub: T.translate('Permission')}}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormPermission;

