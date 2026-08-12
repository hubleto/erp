import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormWorkflowStepProps extends FormProps {}

const componentName = 'FormWorkflowStep'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Workflow';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormWorkflowStepProps) => {
  return <div>
    <Input field='id_workflow' />
    <Input field='name' />
    <Input field='order' />
    <Input field='tag' />
    <Input field='probability' />
  </div>;
}

/** FormWorkflowStep */
const FormWorkflowStep = (props: FormWorkflowStepProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/WorkflowStep'}
    urlSlug='workflow/workflows/steps'
    endpointParams={{}}
    title={{field: 'name', sub: T.translate('Workflow step')}}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormWorkflowStep;
