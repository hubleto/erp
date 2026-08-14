import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormMilestoneTaskProps extends FormProps {}

const componentName = 'FormMilestoneTask'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Projects';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormMilestoneTaskProps) => {
  return <>
    <Input field='id_milestone' />
    <Input field='id_task' />
  </>;
}

/** FormMilestoneTask */
const FormMilestoneTask = (props: FormMilestoneTaskProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/MilestoneTask'}
    urlSlug='projects/milestones/tasks'
    title={{field: 'id_task', sub: T.translate('Milestone Task')}}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormMilestoneTask;
