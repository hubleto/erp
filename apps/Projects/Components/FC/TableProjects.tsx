import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormProject, { FormProjectProps } from './FormProject';

interface TableProjectsProps extends TableProps {
  idDeal?: number,
}

const componentName = 'TableProjects'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Projects';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableProjects = (props: TableProjectsProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Project'}
    endpointParams={{idDeal: props.idDeal}}
    formUrlSlug='projects'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{id_deal: props.idDeal}}
    getRowClassName={(table: TableMeta, rowData: any): string => {
      return rowData.is_closed ? 'bg-slate-300' : table.getDefaultRowClassName(rowData);
    }}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormProject {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableProjects;
