import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormActivityTypeProps extends FormProps {}

const componentName = 'FormActivityType'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Worksheets';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormActivityTypeProps) => {
  return <>
    <Input field='name'/>
  </>;
}

/** FormActivityType */
const FormActivityType = (props: FormActivityTypeProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/ActivityType'}
    urlSlug='worksheet/activity-types'
    endpointParams={{}}
    onAfterFormInitialized={(form: any) => {}}
    title={{field: 'name', sub: T.translate('Activity type')}}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormActivityType;

