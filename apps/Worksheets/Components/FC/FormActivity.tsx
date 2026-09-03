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
  return <div>
    <div className='flex-dyn w-full'>
      <div className='grow'><Input field='id_worker' /></div>
      <div className='grow'><Input field='id_task' /></div>
    </div>
    <div className='flex-dyn w-full'>
      <div className='grow'><Input field='worked_hours' customInputProps={{cssClass: 'text-[1.5em]'}} /></div>
      <div className='grow'><Input field='date_worked' /></div>
    </div>
    <Input field='description' />
  </div>;
}

/** FormActivity */
const FormActivity = (props: FormActivityProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Activity'}
    urlSlug='worksheets'
    title={{fields: ['worked_hours', <>hours, </>, 'description'], sub: T.translate('Activity')}}
    renderTopInputs={() => <div className='modal-top-inputs'>
      <Input field='is_approved' renderOnlyInputField />
      <Input field='is_chargeable' renderOnlyInputField></Input>
      <Input field='id_type' renderOnlyInputField customInputProps={{uiStyle: 'buttons'}} />
    </div>}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormActivity;
