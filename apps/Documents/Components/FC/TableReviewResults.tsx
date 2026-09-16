import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';

interface TableReviewResultsProps extends TableProps {}

const componentName = 'TableReviewResults'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Documents';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableReviewResults = (props: TableReviewResultsProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/ReviewResult'}
    // endpointParams={{idSomeField: props.idSomeField}}
    baseUrlSlug='documents/review-results'
    formModalProps={{type: 'right'}}
    // formDefaultValues={{id_some_field: props.idSomeField}}
    {...props}
  ></Table>
}

export default TableReviewResults;