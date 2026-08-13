import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormTemplate, { FormTemplateProps } from './FormTemplate';

interface TableTemplatesProps extends TableProps {}

const componentName = 'TableTemplates'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Documents';

const TableTemplates = (props: TableTemplatesProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Template'}
    formUrlSlug='documents.templates'
    formModalProps={{type: 'right wide'}}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormTemplate {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableTemplates;
