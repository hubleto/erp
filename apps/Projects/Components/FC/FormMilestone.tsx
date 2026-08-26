import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import TableMilestoneReports from './TableMilestoneReports';
import TableMilestoneTasks from './TableMilestoneTasks';

export interface FormMilestoneProps extends FormProps {}

const componentName = 'FormMilestone'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Projects';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormMilestoneProps) => {
  const form = React.useContext(FormMetaContext);
  return <div className='flex gap-2'>
    <div className='grow'>
      <Input field='id_project' />
      <Input field='id_responsible' />
      <Input field='title' />
      <Input field='date_due' />
      <Input field='description' />
      <Input field='is_closed' />
      {form.id > 0 ? <>
        <div className='grow card'>
          <div className='card-header'>{T.translate('Tasks')}</div>
          <div className='card-body'>
            <TableMilestoneTasks
              tag={"table_project_milestone_task"}
              parentForm={form}
              uid={props.uid + "_table_project_milestone_task"}
              idMilestone={form.id}
            />
          </div>
        </div>
      </> : null}
    </div>
    {form.id > 0 ? <>
      <div className='grow card'>
        <div className='card-header'>{T.translate('Reports')}</div>
        <div className='card-body'>
          <TableMilestoneReports
            tag={"table_project_milestone_report"}
            parentForm={form}
            uid={props.uid + "_table_project_milestone_report"}
            idMilestone={form.id}
          />
        </div>
      </div>
    </> : null}
  </div>;
}

/** FormMilestone */
const FormMilestone = (props: FormMilestoneProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Milestone'}
    urlSlug='projects/milestones'
    endpointParams={{}}
    title={{field: 'title', sub: T.translate('Milestone')}}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormMilestone;