import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormActivityProps extends FormProps {}

const componentName = 'FormActivity'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Worksheets';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormActivityProps) => {
  return <div className="w-full flex gap-2 md:flex-row">
    <div className='w-full'>
      <Input field='id_task' />
      <Input field='id_type' />
      <Input field='date_worked' />
      <Input field='description' />
      <Input field='worked_hours' />
    </div>
    <div className='w-full'>
      <Input field='id_worker' />
      <Input field='is_approved' />
      <Input field='is_chargeable' />
      <Input field='datetime_created' />
    </div>
  </div>;
}

/** FormActivity */
const FormActivity = (props: FormActivityProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Activity'}
    urlSlug='worksheets'
    endpointParams={{}}
    onAfterFormInitialized={(form: any) => {}}
    title={{field: 'id', sub: T.translate('Activity')}}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormActivity;
