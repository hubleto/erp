import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormMilestoneReportProps extends FormProps {}

const componentName = 'FormMilestoneReport'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Projects';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormMilestoneReportProps) => {
  return <>
    <Input field='id_milestone' />
    <Input field='date_report' />
    <Input field='summary' />
    <Input field='details' />
    <Input field='progress_percent' />
    <Input field='id_reported_by' />
  </>;
}

/** FormMilestoneReport */
const FormMilestoneReport = (props: FormMilestoneReportProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/MilestoneReport'}
    urlSlug='projects/milestones/reports'
    title={{field: 'date_report', sub: T.translate('Milestone Report')}}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormMilestoneReport;
