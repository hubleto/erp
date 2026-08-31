import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import TableWorkflowSteps from './TableWorkflowSteps';

export interface FormWorkflowProps extends FormProps {}

const componentName = 'FormWorkflow'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Workflow';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormWorkflowProps) => {
  const form = React.useContext(FormMetaContext);
  return <div className="flex gap-2" >
    <div>
      <div className="card-header">{T.translate('Workflow')}</div>
      <div className="card-body">
        <Input field='name' />
        <Input field='order' />
        <Input field='group' />
        <Input field='description' />
        <Input field='show_in_kanban' />
      </div>
    </div>

    <div>
      <div className="card-header">{T.translate('Steps')}</div>
      <div className="card-body">
        <TableWorkflowSteps
          uid={props.uid + "_table_workflow_steps_input"}
          idWorkflow={form.id}
        ></TableWorkflowSteps>
      </div>
    </div>
  </div>;
}

/** FormWorkflow */
const FormWorkflow = (props: FormWorkflowProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/XXX'}
    urlSlug='workflow/workflows'
    endpointParams={{saveRelations: ['STEPS']}}
    title={{field: 'name', sub: T.translate('Workflow')}}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormWorkflow;

