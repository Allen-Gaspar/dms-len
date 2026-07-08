import numpy as allen
import pandas as cha

arr1 = allen.array([10, 20, 30, 40, 50])
print("Array:", arr1)
print("--" * 10)

print("First Element:", arr1[0])
print("Last Element:", arr1[4])
print("Third Element:", arr1[2])
print("--" * 10)


print("Elements from index 1 to 3:", arr1[1:3])
print("First three elements:", arr1[:3])
print("--" * 10)

arr2 = allen.array([1, 2, 3, 4, 5]) 
print("1. Data type:", arr2.dtype) 

arr2f = arr2.astype(float)
print("2. Converted array:", arr2f) 
print("3. Data type:", arr2f.dtype)
print("--" * 10)

s1 = [100, 200, 300, 400] 
print(cha.Series(s1))   

print("Second value:", s1[1]) 
print("--" * 10)


datas = {
    'Name': ['Ana', 'Ben', 'Carl'],
    'Age': [20, 21, 19],
    'Score': [85, 90, 88]
} 

df1 = cha.DataFrame(datas) 
print(df1)
print("--" * 10)
print(df1['Name'])
print("--" * 10)
print(df1['Age'])
print("--" * 10)


df_csv = cha.read_csv('students.csv')
print(df_csv)
print("--" * 10)

print(df_csv['Name'])
print("--" * 10)
print(df_csv['Grade'])
print("--" * 10)
print(df_csv.head(2))
print("--" * 10)