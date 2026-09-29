import Translator from '@hubleto/react-ui/core/Translator';
import Form from '@hubleto/react-ui/components/fc/Form';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormHrCourseProps extends FormProps {}

const componentName = 'FormHrCourse';
const parentApp = 'Hubleto/App/Community/HrPerformance';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const FormHrCourse = (props: FormHrCourseProps) => <Form
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/Course'}
  urlSlug='hr-performance/courses'
  title={{field: 'name', sub: T.translate('Learning course')}}
  tabs={{default: {content: () => <div className='grid grid-cols-1 md:grid-cols-2 gap-2'>
    <Input field='name' />
    <Input field='provider' />
    <Input field='delivery' />
    <Input field='duration_hours' />
    <Input field='url' />
    <Input field='is_active' />
    <Input field='description' />
  </div>}}}
  {...props}
/>

export default FormHrCourse;