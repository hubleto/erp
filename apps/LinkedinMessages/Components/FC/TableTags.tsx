import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { type TableMeta, type TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormTag from './FormTag';

interface TableTagsProps extends TableProps {
}

const componentName = 'TableTags'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Custom/LinkedinMessages';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableTags = (props: TableTagsProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Tag'}
    endpointParams={{}}
    baseUrlSlug='linkedin-messages/tags'
    formModalProps={{type: 'centered'}}
    formDefaultValues={{}}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormTag {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableTags;
