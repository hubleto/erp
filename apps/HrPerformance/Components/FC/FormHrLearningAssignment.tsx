import Translator from '@hubleto/react-ui/core/Translator';
import Form from '@hubleto/react-ui/components/fc/Form';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormHrLearningAssignmentProps extends FormProps {}

const componentName = 'FormHrLearningAssignment';
const parentApp = 'Hubleto/App/Community/HrPerformance';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const FormHrLearningAssignment = (props: FormHrLearningAssignmentProps) => <Form
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/LearningAssignment'}
  urlSlug='hr-performance/learning'
  title={{fields: ['id_course', 'status'], sub: T.translate('Learning assignment')}}
  tabs={{default: {content: () => <div className='grid grid-cols-1 md:grid-cols-2 gap-2'>
    <Input field='id_user' />
    <Input field='id_course' />
    <Input field='date_assigned' />
    <Input field='date_due' />
    <Input field='date_completed' />
    <Input field='status' />
    <Input field='notes' />
  </div>}}}
  {...props}
/>

export default FormHrLearningAssignment;